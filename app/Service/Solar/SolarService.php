<?php

namespace App\Services\Solar;

use App\Models\Cable;
use App\Models\Inverter;
use App\Models\Panel;
use App\Models\Project;
use App\Models\Protection;
use App\Services\Solar\Calculator\SolarPhysicsCalculator;
use App\Services\Solar\Finance\CostCalculator;
use App\Services\Solar\Simulation\ProductionSimulator;
use App\Services\Solar\Standards\SolarStandards;
use App\Services\Solar\Structure\MassifCalculator;
use App\Services\Solar\Structure\StructureCalculator;
use App\Services\Solar\Validators\SolarValidator;
use Illuminate\Support\Arr;

class SolarService
{
    public function __construct(
        private readonly SolarPhysicsCalculator $calc,
        private readonly SolarStandards $std,
        private readonly SolarValidator $val,
        private readonly StructureCalculator $structure,
        private readonly MassifCalculator $massif,
        private readonly CostCalculator $cost,
        private readonly ProductionSimulator $simulator,
        private readonly WeatherService $weather
    ) {
    }

    public function calculate(array $data, bool $storeComparison = true): array
    {
        $input = $this->prepareInput($data);

        $panelModel = $this->resolvePanel($input);
        $coords = $this->resolveCoordinates($input);
        $irradiationYearly = $this->resolveIrradiation($input, $coords);

        $losses = [
            'temperature' => $input['temperature_loss_percent'],
            'inverter' => $input['inverter_loss_percent'],
            'dust' => $input['dust_loss_percent'],
            'other' => $input['other_loss_percent'],
        ];

        $calculatedPr = $this->calc->calculatePerformanceRatio($losses);
        $performanceRatio = min($input['performance_ratio'], $calculatedPr);
        $dailyIrradiation = round($irradiationYearly / 365, 3);

        $requiredKw = $this->calc->calculateRequiredPvPower(
            $input['daily_consumption'],
            $dailyIrradiation,
            $performanceRatio
        );

        $panelCount = $this->calc->calculatePanelCount($requiredKw, (float) $panelModel->power);
        $realPvKw = round(($panelCount * (float) $panelModel->power) / 1000, 2);
        $arrayArea = $this->calc->calculateArrayArea(
            $panelCount,
            (float) ($panelModel->length_m ?? 2.1),
            (float) ($panelModel->width_m ?? 1.05)
        );

        $surfaceValidation = $this->val->surface($arrayArea, $input['available_surface']);
        $loadProfile = $this->calc->calculateLoadSplit(
            $input['daily_consumption'],
            $input['day_usage_percent'],
            $input['night_usage_percent']
        );

        $inverterModel = $this->resolveInverter($input, $realPvKw);
        $inverterCount = (int) ceil($realPvKw / max((float) ($inverterModel->ac_power_kw ?? $inverterModel->power), 0.1));
        $dcAcRatio = round($realPvKw / max(((float) ($inverterModel->ac_power_kw ?? $inverterModel->power) * $inverterCount), 0.1), 2);
        $inverterValidation = $this->val->inverter(
            $realPvKw,
            (float) ($inverterModel->ac_power_kw ?? $inverterModel->power) * $inverterCount
        );

        $stringDesign = $this->designStrings($panelModel, $inverterModel, $panelCount, $input);
        $mpptValidation = $this->val->mppt(
            $stringDesign['within_voltage_limits'],
            $stringDesign['within_current_limits'],
            $stringDesign['strings'],
            $stringDesign['max_strings_capacity']
        );

        $acVoltage = (float) ($inverterModel->nominal_ac_voltage ?: ($input['installation_type'] === 'Industrial' ? 400 : 230));
        $acThreePhase = $acVoltage >= 380;
        $dcCurrent = round($stringDesign['parallel_strings_per_mppt'] * (float) ($panelModel->imp ?? 10), 2);
        $acCurrent = $this->calc->calculateCurrent(
            (float) ($inverterModel->ac_power_kw ?? $inverterModel->power) * $inverterCount,
            $acVoltage,
            0.95,
            $acThreePhase
        );

        $dcCable = $this->selectCable($input, 'DC', $dcCurrent, max($input['cable_length'] * 0.6, 1), (float) $stringDesign['design_voltage']);
        $acCable = $this->selectCable($input, 'AC', $acCurrent, $input['cable_length'], $acVoltage);

        $structureData = $this->structure->calculateStructure([
            'panels' => $panelCount,
            'panel_length_m' => (float) ($panelModel->length_m ?? 2.1),
            'panel_width_m' => (float) ($panelModel->width_m ?? 1.05),
            'tilt_angle' => $input['tilt_angle'],
            'mounting_type' => $input['mounting_type'],
            'structure_type' => $input['structure_type'],
            'available_surface' => $input['available_surface'],
            'latitude' => $coords['lat'],
        ]);

        $windPressure = $this->structure->windLoadFactor($input['installation_type'] === 'Industrial' ? 145 : 120);
        $massifData = $this->massif->calculateMassifs($panelCount, $structureData['rows'], $input['mounting_type']);
        $concreteVolume = $input['mounting_type'] === 'ground'
            ? $this->massif->concreteVolume($massifData['massifs_count'])
            : 0;

        $protections = $this->designProtection($dcCurrent, $acCurrent);
        $protectionValidation = $this->val->protection(
            max($dcCurrent, $acCurrent),
            $protections['breaker'],
            $protections['fuse'],
            $protections['spd']
        );

        $orientationFactor = $this->orientationFactor($input['azimuth']);
        $tiltFactor = $this->tiltFactor($input['tilt_angle'], $structureData['optimized_tilt_deg']);
        $annualProduction = $this->calc->calculateAnnualProduction(
            $realPvKw,
            $irradiationYearly,
            $performanceRatio,
            $orientationFactor,
            $tiltFactor
        );
        $monthlyProduction = $this->simulator->monthlyProduction(
            $annualProduction,
            $input['day_usage_percent'],
            $input['structure_type']
        );
        $annualProduction = $this->simulator->yearlyProduction($monthlyProduction);
        $annualProductionAfterDegradation = round($annualProduction * (1 - ($input['degradation'] / 100)), 2);

        $panelCost = $this->cost->calculatePanelCost($panelCount, (float) $panelModel->power);
        $inverterCost = $this->cost->calculateInverterCost(
            (float) ($inverterModel->ac_power_kw ?? $inverterModel->power),
            $inverterCount,
            (string) $inverterModel->type
        );
        $dcCableCost = $this->cost->calculateCableCost($dcCable['length'], $dcCable['cost_per_meter'], max($stringDesign['strings'], 1) * 2);
        $acCableCost = $this->cost->calculateCableCost($acCable['length'], $acCable['cost_per_meter'], 1);
        $structureCost = $this->cost->calculateStructureCost($realPvKw, $input['mounting_type'], $input['structure_type']);
        $massifCost = $this->cost->calculateMassifCost($concreteVolume);
        $protectionCost = $this->cost->calculateProtectionCost($protections);

        $materialTotal = $this->cost->totalCost([
            $panelCost,
            $inverterCost,
            $dcCableCost,
            $acCableCost,
            $structureCost,
            $massifCost,
            $protectionCost,
        ]);
        $installationCost = $this->cost->calculateInstallationCost($materialTotal, $input['installation_type']);
        $totalInvestment = round($materialTotal + $installationCost, 2);
        $annualSavings = round(min($annualProductionAfterDegradation, $input['daily_consumption'] * 365) * $input['price_per_kwh'], 2);
        $roi = $this->cost->calculateROI($totalInvestment, $annualSavings);
        $payback = $roi;
        $tenYearProfit = round(($annualSavings * 10) - $totalInvestment, 2);

        $checks = [
            'surface' => $surfaceValidation,
            'inverter' => $inverterValidation,
            'mppt' => $mpptValidation,
            'dc_drop' => $dcCable['validation'],
            'ac_drop' => $acCable['validation'],
            'protection' => $protectionValidation,
        ];

        $global = $this->val->global($checks);
        $suggestions = $this->buildSuggestions($surfaceValidation, $inverterValidation, $mpptValidation, $global, $input);

        $formulas = [
            'pv_power' => 'Ppv = E_daily / (H_daily × PR)',
            'panel_count' => 'N = Ppv / Ppanel',
            'dc_ac_ratio' => 'DC/AC = Pdc / Pac',
            'cable_section' => 'S = 2 × rho × L × I / deltaV',
            'roi' => 'ROI = Total Investment / Annual Savings',
        ];

        $result = [
            'input' => [
                'project_id' => $input['project_id'],
                'project_name' => $input['project_name'],
                'city' => $input['city'],
                'country' => $input['country'],
                'installation_type' => $input['installation_type'],
                'daily_consumption' => $input['daily_consumption'],
                'peak_power' => $input['peak_power'],
                'day_usage_percent' => $input['day_usage_percent'],
                'night_usage_percent' => $input['night_usage_percent'],
                'autonomy_days' => $input['autonomy_days'],
                'tilt_angle' => $input['tilt_angle'],
                'azimuth' => $input['azimuth'],
                'available_surface' => $input['available_surface'],
                'mounting_type' => $input['mounting_type'],
                'structure_type' => $input['structure_type'],
                'temperature_min' => $input['temperature_min'],
                'temperature_max' => $input['temperature_max'],
                'cable_length' => $input['cable_length'],
                'price_per_kwh' => $input['price_per_kwh'],
                'performance_ratio' => $performanceRatio,
                'losses' => $losses,
                'degradation' => $input['degradation'],
                'budget' => $input['budget'],
                'latitude' => $coords['lat'],
                'longitude' => $coords['lon'],
                'use_auto_irradiation' => $input['use_auto_irradiation'],
            ],
            'solar' => [
                'required_kw' => $requiredKw,
                'real_kw' => $realPvKw,
                'panels' => $panelCount,
                'selected_panel_power' => (float) $panelModel->power,
                'surface_used_m2' => $arrayArea,
                'surface_validation' => $surfaceValidation,
                'load_profile' => $loadProfile,
            ],
            'irradiation' => $irradiationYearly,
            'production' => [
                'monthly_kwh' => $monthlyProduction,
                'yearly_kwh' => $annualProduction,
                'yearly_kwh_after_degradation' => $annualProductionAfterDegradation,
                'specific_yield' => round($annualProduction / max($realPvKw, 0.1), 2),
            ],
            'panel' => [
                'model' => $panelModel->model,
                'voc' => (float) $panelModel->voc,
                'vmp' => (float) $panelModel->vmp,
                'isc' => (float) $panelModel->isc,
                'imp' => (float) $panelModel->imp,
                'efficiency' => (float) $panelModel->efficiency,
                'length_m' => (float) $panelModel->length_m,
                'width_m' => (float) $panelModel->width_m,
            ],
            'inverter' => [
                'model' => $inverterModel->model,
                'value' => (float) ($inverterModel->ac_power_kw ?? $inverterModel->power),
                'count' => $inverterCount,
                'dc_ac_ratio' => $dcAcRatio,
                'validation' => $inverterValidation,
                'type' => $inverterModel->type ?? 'Standard',
                'mppt_count' => (int) ($inverterModel->mppt_count ?? 1),
                'strings_per_mppt' => (int) ($inverterModel->strings_per_mppt ?? 1),
                'mppt_min_voltage' => (float) ($inverterModel->mppt_min_voltage ?? 120),
                'mppt_max_voltage' => (float) ($inverterModel->mppt_max_voltage ?? 550),
                'max_dc_voltage' => (float) ($inverterModel->max_dc_voltage ?? 600),
                'max_input_current' => (float) ($inverterModel->max_input_current ?? 20),
            ],
            'stringing' => $stringDesign,
            'cable' => [
                'dc' => $dcCable,
                'ac' => $acCable,
            ],
            'protection' => [
                'breaker' => $protections['breaker'],
                'fuse' => $protections['fuse'],
                'spd' => $protections['spd'],
                'earthing' => $protections['earthing'],
                'validation' => $protectionValidation,
            ],
            'structure' => array_merge($structureData, [
                'wind_pressure_kN_m2' => $windPressure,
            ]),
            'massifs' => [
                'count' => $massifData['massifs_count'],
                'supports_per_row' => $massifData['supports_per_row'],
                'concrete_volume_m3' => $concreteVolume,
            ],
            'costs' => [
                'panel' => $panelCost,
                'inverter' => $inverterCost,
                'dc_cable' => $dcCableCost,
                'ac_cable' => $acCableCost,
                'cable' => round($dcCableCost + $acCableCost, 2),
                'structure' => $structureCost,
                'massifs' => $massifCost,
                'protection' => $protectionCost,
                'material_total' => $materialTotal,
                'installation' => $installationCost,
                'total' => $totalInvestment,
                'price_per_kwh' => $input['price_per_kwh'],
                'annual_savings' => $annualSavings,
                'monthly_revenue' => round($annualSavings / 12, 2),
                'annual_revenue' => $annualSavings,
                'ten_year_profit' => $tenYearProfit,
                'roi_years' => $roi,
                'payback_years' => $payback,
                'budget_status' => $input['budget'] > 0
                    ? ($input['budget'] >= $totalInvestment ? 'within-budget' : 'over-budget')
                    : 'not-provided',
            ],
            'checks' => $checks,
            'global' => $global,
            'suggestions' => $suggestions,
            'formulas' => $formulas,
        ];

        if ($storeComparison) {
            $comparison = $this->compareScenarios($input);
            $result['comparison'] = $comparison['scenarios'];
            $result['recommended_scenario'] = $comparison['recommended'];
        }

        return $result;
    }

    public function calculateAndStore(array $data): Project
    {
        $result = $this->calculate($data, storeComparison: true);
        $input = $result['input'];
        $inputHash = hash('sha256', json_encode(Arr::except($input, ['project_id'])));

        $payload = [
            'name' => $input['project_name'],
            'location' => $input['city'] . ', ' . $input['country'],
            'consumption' => $input['daily_consumption'],
            'surface' => $input['available_surface'],
            'budget' => $input['budget'],
            'city' => $input['city'],
            'input_hash' => $inputHash,
            'input_data' => [
                'form' => Arr::except($input, ['latitude', 'longitude']),
                'coordinates' => [
                    'lat' => $input['latitude'],
                    'lon' => $input['longitude'],
                ],
                'snapshot' => Arr::except($result, ['input']),
            ],
            'required_kw' => $result['solar']['required_kw'],
            'real_kw' => $result['solar']['real_kw'],
            'panels' => $result['solar']['panels'],
            'production' => [
                'monthly' => $result['production']['monthly_kwh'],
                'yearly' => $result['production']['yearly_kwh'],
                'yearly_after_degradation' => $result['production']['yearly_kwh_after_degradation'],
            ],
            'costs' => $result['costs'],
            'roi_years' => $result['costs']['roi_years'],
        ];

        if (! empty($input['project_id'])) {
            $project = Project::findOrFail((int) $input['project_id']);
            $project->fill($payload)->save();

            return $project;
        }

        return Project::firstOrCreate(
            ['input_hash' => $inputHash],
            $payload
        );
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }

    public function projectToDashboardPayload(Project $project): array
    {
        $snapshot = data_get($project->input_data, 'snapshot', []);
        $form = data_get($project->input_data, 'form', []);

        $result = array_replace_recursive([
            'input' => [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'city' => data_get($form, 'city', $project->city),
                'country' => data_get($form, 'country', ''),
                'installation_type' => data_get($form, 'installation_type', 'Residential'),
                'daily_consumption' => data_get($form, 'daily_consumption', data_get($form, 'consumption')),
                'peak_power' => data_get($form, 'peak_power'),
                'day_usage_percent' => data_get($form, 'day_usage_percent', 60),
                'night_usage_percent' => data_get($form, 'night_usage_percent', 40),
                'autonomy_days' => data_get($form, 'autonomy_days', 0),
                'tilt_angle' => data_get($form, 'tilt_angle', 25),
                'azimuth' => data_get($form, 'azimuth', 0),
                'available_surface' => data_get($form, 'available_surface', $project->surface),
                'mounting_type' => data_get($form, 'mounting_type', 'roof'),
                'structure_type' => data_get($form, 'structure_type', 'fixed'),
                'temperature_min' => data_get($form, 'temperature_min', 0),
                'temperature_max' => data_get($form, 'temperature_max', 45),
                'cable_length' => data_get($form, 'cable_length', 20),
                'price_per_kwh' => data_get($project->costs, 'price_per_kwh', 1.2),
                'performance_ratio' => data_get($form, 'performance_ratio', 0.8),
                'losses' => data_get($form, 'losses', []),
                'degradation' => data_get($form, 'degradation', 0.5),
                'budget' => data_get($form, 'budget', $project->budget),
                'latitude' => data_get($project->input_data, 'coordinates.lat'),
                'longitude' => data_get($project->input_data, 'coordinates.lon'),
                'use_auto_irradiation' => data_get($form, 'use_auto_irradiation', true),
            ],
            'solar' => [
                'required_kw' => (float) ($project->required_kw ?? 0),
                'real_kw' => (float) ($project->real_kw ?? 0),
                'panels' => (int) ($project->panels ?? 0),
                'selected_panel_power' => 0,
                'surface_used_m2' => 0,
                'surface_validation' => ['status' => 'legacy', 'message' => 'Legacy project payload'],
                'load_profile' => [
                    'day_kwh' => 0,
                    'night_kwh' => 0,
                ],
            ],
            'irradiation' => 0,
            'production' => [
                'monthly_kwh' => data_get($project->production, 'monthly', []),
                'yearly_kwh' => (float) data_get($project->production, 'yearly', 0),
                'yearly_kwh_after_degradation' => (float) data_get($project->production, 'yearly_after_degradation', data_get($project->production, 'yearly', 0)),
                'specific_yield' => 0,
            ],
            'panel' => [
                'model' => 'Legacy panel data',
                'voc' => 0,
                'vmp' => 0,
                'isc' => 0,
                'imp' => 0,
                'efficiency' => 0,
                'length_m' => 0,
                'width_m' => 0,
            ],
            'inverter' => [
                'model' => 'Legacy inverter data',
                'value' => 0,
                'count' => 0,
                'dc_ac_ratio' => 0,
                'validation' => ['status' => 'legacy'],
                'type' => 'Legacy',
                'mppt_count' => 0,
                'strings_per_mppt' => 0,
                'mppt_min_voltage' => 0,
                'mppt_max_voltage' => 0,
                'max_dc_voltage' => 0,
                'max_input_current' => 0,
            ],
            'stringing' => [
                'strings' => 0,
                'panels_per_string' => 0,
                'mppt_used' => 0,
                'parallel_strings_per_mppt' => 0,
                'max_strings_capacity' => 0,
                'string_voc' => 0,
                'string_vmp' => 0,
                'string_current' => 0,
                'design_voltage' => 0,
                'within_voltage_limits' => false,
                'within_current_limits' => false,
            ],
            'cable' => [
                'dc' => [
                    'section' => 0,
                    'standard' => 0,
                    'length' => 0,
                    'material' => 'Legacy',
                    'ampacity' => 0,
                    'cost_per_meter' => 0,
                    'voltage_drop_percent' => 0,
                    'validation' => ['status' => 'legacy'],
                ],
                'ac' => [
                    'section' => 0,
                    'standard' => 0,
                    'length' => 0,
                    'material' => 'Legacy',
                    'ampacity' => 0,
                    'cost_per_meter' => 0,
                    'voltage_drop_percent' => 0,
                    'validation' => ['status' => 'legacy'],
                ],
            ],
            'protection' => [
                'breaker' => 0,
                'fuse' => 0,
                'spd' => 0,
                'earthing' => 0,
                'validation' => ['status' => 'legacy'],
            ],
            'structure' => [
                'mounting_type' => data_get($form, 'mounting_type', 'roof'),
                'structure_type' => data_get($form, 'structure_type', 'fixed'),
                'rows' => 0,
                'panels_per_row' => 0,
                'row_spacing_m' => 0,
                'total_length_m' => 0,
                'total_width_m' => 0,
                'footprint_m2' => 0,
                'surface_usage_percent' => 0,
                'optimized_tilt_deg' => 0,
                'weight_distribution_kg_m2' => 0,
                'wind_pressure_kN_m2' => 0,
            ],
            'massifs' => [
                'count' => 0,
                'supports_per_row' => 0,
                'concrete_volume_m3' => 0,
            ],
            'costs' => array_merge([
                'panel' => 0,
                'inverter' => 0,
                'dc_cable' => 0,
                'ac_cable' => 0,
                'cable' => (float) data_get($project->costs, 'cable', 0),
                'structure' => 0,
                'massifs' => 0,
                'protection' => 0,
                'material_total' => 0,
                'installation' => (float) data_get($project->costs, 'installation', 0),
                'total' => (float) data_get($project->costs, 'total', 0),
                'price_per_kwh' => (float) data_get($project->costs, 'price_per_kwh', 1.2),
                'annual_savings' => (float) data_get($project->costs, 'annual_revenue', 0),
                'monthly_revenue' => (float) data_get($project->costs, 'monthly_revenue', 0),
                'annual_revenue' => (float) data_get($project->costs, 'annual_revenue', 0),
                'ten_year_profit' => (float) data_get($project->costs, 'ten_year_profit', 0),
                'roi_years' => (float) data_get($project->costs, 'roi_years', $project->roi_years ?? 0),
                'payback_years' => (float) data_get($project->costs, 'payback_years', data_get($project->costs, 'roi_years', $project->roi_years ?? 0)),
                'budget_status' => data_get($project->costs, 'budget_status', 'legacy'),
            ], $project->costs ?? []),
            'checks' => [],
            'global' => data_get($snapshot, 'global', ['status' => 'legacy']),
            'suggestions' => data_get($snapshot, 'suggestions', [
                'This project was created with an older payload format and has been safely normalized for the new dashboard.',
            ]),
            'formulas' => data_get($snapshot, 'formulas', [
                'pv_power' => 'Ppv = E_daily / (H_daily × PR)',
                'panel_count' => 'N = Ppv / Ppanel',
                'dc_ac_ratio' => 'DC/AC = Pdc / Pac',
                'cable_section' => 'S = 2 × rho × L × I / deltaV',
                'roi' => 'ROI = Total Investment / Annual Savings',
            ]),
        ], $snapshot, [
            'input' => [
                'project_id' => $project->id,
                'project_name' => $project->name,
            ],
        ]);

        return [
            'result' => $result,
            'comparison' => data_get($snapshot, 'comparison', []),
            'recommendedScenario' => data_get($snapshot, 'recommended_scenario'),
            'formData' => array_merge($form, [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'city' => data_get($form, 'city', $project->city),
                'country' => data_get($form, 'country', ''),
                'daily_consumption' => data_get($form, 'daily_consumption', data_get($form, 'consumption', 10)),
                'available_surface' => data_get($form, 'available_surface', $project->surface),
                'budget' => data_get($form, 'budget', $project->budget),
                'price_per_kwh' => data_get($project->costs, 'price_per_kwh', 1.2),
                'latitude' => data_get($project->input_data, 'coordinates.lat'),
                'longitude' => data_get($project->input_data, 'coordinates.lon'),
            ]),
        ];
    }

    private function compareScenarios(array $input): array
    {
        $panels = Panel::query()->whereNull('project_id')->orderByDesc('power')->limit(3)->get();
        $scenarios = [];

        foreach ($panels as $panel) {
            $scenario = $this->calculate(array_merge($input, ['panel_id' => $panel->id]), false);
            $scenarios[] = [
                'panel_power' => $panel->power,
                'panel_model' => $panel->model,
                'panels' => $scenario['solar']['panels'],
                'real_kw' => $scenario['solar']['real_kw'],
                'surface_used_m2' => $scenario['solar']['surface_used_m2'],
                'total_cost' => $scenario['costs']['total'],
                'roi_years' => $scenario['costs']['roi_years'],
                'annual_revenue' => $scenario['costs']['annual_revenue'],
                'status' => $scenario['global']['status'],
            ];
        }

        usort($scenarios, fn (array $a, array $b) => [$a['roi_years'], $a['total_cost']] <=> [$b['roi_years'], $b['total_cost']]);

        return [
            'scenarios' => $scenarios,
            'recommended' => $scenarios[0] ?? null,
        ];
    }

    private function designStrings(Panel $panel, Inverter $inverter, int $panelCount, array $input): array
    {
        $mpptCount = max((int) ($inverter->mppt_count ?? 1), 1);
        $stringsPerMppt = max((int) ($inverter->strings_per_mppt ?? 1), 1);
        $maxStringsCapacity = $mpptCount * $stringsPerMppt;

        $minByVoltage = max(1, (int) floor(($inverter->mppt_min_voltage ?? 120) / max((float) ($panel->vmp ?? 40), 1)));
        $maxByVoltage = max(1, (int) floor(min(($inverter->mppt_max_voltage ?? 550), ($inverter->max_dc_voltage ?? 600)) / max((float) ($panel->voc ?? 49), 1)));
        $targetStrings = max(1, min($maxStringsCapacity, (int) ceil($panelCount / max($maxByVoltage, 1))));
        $panelsPerString = (int) max($minByVoltage, min($maxByVoltage, ceil($panelCount / $targetStrings)));
        $strings = (int) ceil($panelCount / max($panelsPerString, 1));
        $parallelStringsPerMppt = (int) ceil($strings / $mpptCount);

        $stringVoc = $this->calc->calculateStringVoc(
            (float) ($panel->voc ?? 49),
            $panelsPerString,
            $input['temperature_min'],
            (float) ($panel->temperature_coefficient ?? -0.32)
        );
        $stringVmp = $this->calc->calculateStringVmp(
            (float) ($panel->vmp ?? 41),
            $panelsPerString,
            $input['temperature_max'],
            (float) ($panel->temperature_coefficient ?? -0.32)
        );
        $stringCurrent = round($parallelStringsPerMppt * (float) ($panel->imp ?? 10), 2);

        return [
            'strings' => $strings,
            'panels_per_string' => $panelsPerString,
            'mppt_used' => min($mpptCount, $strings),
            'parallel_strings_per_mppt' => $parallelStringsPerMppt,
            'max_strings_capacity' => $maxStringsCapacity,
            'string_voc' => $stringVoc,
            'string_vmp' => $stringVmp,
            'string_current' => $stringCurrent,
            'design_voltage' => $stringVmp,
            'within_voltage_limits' => $stringVoc <= ($inverter->max_dc_voltage ?? 600) && $stringVmp >= ($inverter->mppt_min_voltage ?? 120),
            'within_current_limits' => $stringCurrent <= ($inverter->max_input_current ?? 20),
        ];
    }

    private function selectCable(array $input, string $type, float $current, float $length, float $voltage): array
    {
        $material = 'Copper';
        $computedSection = $this->calc->calculateCableSection($length, $current, 3, $voltage, $material);
        $selected = Cable::query()
            ->whereNull('project_id')
            ->where(function ($query) use ($type) {
                $query->whereNull('voltage_type')->orWhere('voltage_type', $type);
            })
            ->where('section', '>=', $computedSection)
            ->where(function ($query) use ($current) {
                $query->whereNull('ampacity')->orWhere('ampacity', '>=', $current);
            })
            ->orderBy('section')
            ->first();

        if (! $selected && ! empty($input['cable_id'])) {
            $selected = Cable::find($input['cable_id']);
        }

        $section = (float) ($selected?->section ?? $this->std->getStandardValue($computedSection, $this->std->cableStandards()));
        $drop = round(((2 * 0.0175 * $length * $current) / max($section, 0.1)) / max($voltage, 1) * 100, 2);

        return [
            'section' => $computedSection,
            'standard' => $section,
            'length' => round($length, 2),
            'material' => $selected?->material ?? $material,
            'ampacity' => (float) ($selected?->ampacity ?? ($current * 1.25)),
            'cost_per_meter' => (float) ($selected?->cost_per_meter ?? 28),
            'voltage_drop_percent' => $drop,
            'validation' => $this->val->voltageDrop($drop),
        ];
    }

    private function designProtection(float $dcCurrent, float $acCurrent): array
    {
        $baseCurrent = max($dcCurrent, $acCurrent);
        $breaker = $this->standardProtection($baseCurrent * 1.25, 'Breaker');
        $fuse = $this->standardProtection($dcCurrent * 1.25, 'Fuse');
        $spd = $this->standardProtection($baseCurrent * 1.15, 'SPD');
        $earthing = $this->standardProtection($baseCurrent, 'Earthing');

        return [
            'breaker' => $breaker,
            'fuse' => $fuse,
            'spd' => $spd,
            'earthing' => $earthing,
        ];
    }

    private function standardProtection(float $target, string $type): float
    {
        return (float) (Protection::query()
            ->whereNull('project_id')
            ->where('type', $type)
            ->where('rating', '>=', $target)
            ->orderBy('rating')
            ->value('rating')
            ?? $this->std->getStandardValue($target, $this->std->protectionStandards()));
    }

    private function resolveCoordinates(array $input): array
    {
        if ($input['latitude'] !== null && $input['longitude'] !== null) {
            return ['lat' => $input['latitude'], 'lon' => $input['longitude']];
        }

        return $this->weather->getCoordinatesFromCity($input['city']);
    }

    private function resolveIrradiation(array $input, array $coords): float
    {
        if (! $input['use_auto_irradiation'] && $input['irradiation'] !== null) {
            $manualValue = (float) $input['irradiation'];

            return $manualValue <= 50 ? round($manualValue * 365, 2) : $manualValue;
        }

        return round($this->weather->getIrradiation($coords['lat'], $coords['lon']) * 365, 2);
    }

    private function resolvePanel(array $input): Panel
    {
        if (! empty($input['panel_id'])) {
            return Panel::findOrFail($input['panel_id']);
        }

        return Panel::query()->whereNull('project_id')->orderByDesc('power')->firstOrFail();
    }

    private function resolveInverter(array $input, float $realPvKw): Inverter
    {
        if (! empty($input['inverter_id'])) {
            return Inverter::findOrFail($input['inverter_id']);
        }

        return Inverter::query()
            ->whereNull('project_id')
            ->where('max_dc_power_kw', '>=', $realPvKw * 0.95)
            ->orderBy('ac_power_kw')
            ->first()
            ?? Inverter::query()->whereNull('project_id')->orderBy('ac_power_kw')->firstOrFail();
    }

    private function orientationFactor(float $azimuth): float
    {
        return round(max(0.78, 1 - (abs($azimuth) / 180) * 0.18), 3);
    }

    private function tiltFactor(float $tilt, float $optimizedTilt): float
    {
        $difference = abs($tilt - $optimizedTilt);

        return round(max(0.82, 1 - ($difference / 90) * 0.18), 3);
    }

    private function buildSuggestions(array $surface, array $inverter, array $mppt, array $global, array $input): array
    {
        $suggestions = [];

        if ($surface['status'] === 'insufficient') {
            $suggestions[] = 'Surface is insufficient. Reduce required PV power, switch to higher-efficiency panels, or move to a ground structure.';
        }

        if ($inverter['status'] === 'under-sized') {
            $suggestions[] = 'Choose a larger inverter or split the system across more inverters to keep the DC/AC ratio within range.';
        }

        if ($mppt['status'] === 'warning') {
            $suggestions[] = 'Stringing needs review. Adjust panels per string or select an inverter with wider MPPT voltage/current capability.';
        }

        if (($input['budget'] ?? 0) > 0) {
            $suggestions[] = 'Budget check is included in the financial section so you can compare investment against the current target.';
        }

        if ($global['status'] === 'approved') {
            $suggestions[] = 'The current design is technically coherent for a first-pass engineering estimate and suitable for detailed vendor validation.';
        }

        return $suggestions;
    }

    private function prepareInput(array $data): array
    {
        return [
            'project_id' => isset($data['project_id']) && $data['project_id'] !== '' ? (int) $data['project_id'] : null,
            'project_name' => (string) ($data['project_name'] ?? 'PV Project'),
            'city' => (string) ($data['city'] ?? 'Casablanca'),
            'country' => (string) ($data['country'] ?? 'Morocco'),
            'latitude' => isset($data['latitude']) && $data['latitude'] !== '' ? (float) $data['latitude'] : null,
            'longitude' => isset($data['longitude']) && $data['longitude'] !== '' ? (float) $data['longitude'] : null,
            'installation_type' => (string) ($data['installation_type'] ?? 'Residential'),
            'daily_consumption' => (float) ($data['daily_consumption'] ?? 10),
            'peak_power' => (float) ($data['peak_power'] ?? 5),
            'day_usage_percent' => (float) ($data['day_usage_percent'] ?? 60),
            'night_usage_percent' => (float) ($data['night_usage_percent'] ?? 40),
            'autonomy_days' => (float) ($data['autonomy_days'] ?? 0),
            'irradiation' => isset($data['irradiation']) && $data['irradiation'] !== '' ? (float) $data['irradiation'] : null,
            'use_auto_irradiation' => filter_var($data['use_auto_irradiation'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'tilt_angle' => (float) ($data['tilt_angle'] ?? 25),
            'azimuth' => (float) ($data['azimuth'] ?? 0),
            'available_surface' => (float) ($data['available_surface'] ?? 40),
            'mounting_type' => (string) ($data['mounting_type'] ?? 'roof'),
            'structure_type' => (string) ($data['structure_type'] ?? 'fixed'),
            'temperature_min' => (float) ($data['temperature_min'] ?? 0),
            'temperature_max' => (float) ($data['temperature_max'] ?? 45),
            'cable_length' => (float) ($data['cable_length'] ?? 25),
            'price_per_kwh' => (float) ($data['price_per_kwh'] ?? 1.2),
            'panel_id' => isset($data['panel_id']) && $data['panel_id'] !== '' ? (int) $data['panel_id'] : null,
            'inverter_id' => isset($data['inverter_id']) && $data['inverter_id'] !== '' ? (int) $data['inverter_id'] : null,
            'cable_id' => isset($data['cable_id']) && $data['cable_id'] !== '' ? (int) $data['cable_id'] : null,
            'performance_ratio' => (float) ($data['performance_ratio'] ?? 0.8),
            'temperature_loss_percent' => (float) ($data['temperature_loss_percent'] ?? 6),
            'inverter_loss_percent' => (float) ($data['inverter_loss_percent'] ?? 3),
            'dust_loss_percent' => (float) ($data['dust_loss_percent'] ?? 2),
            'other_loss_percent' => (float) ($data['other_loss_percent'] ?? 2),
            'degradation' => (float) ($data['degradation'] ?? 0.5),
            'budget' => isset($data['budget']) && $data['budget'] !== '' ? (float) $data['budget'] : 0.0,
        ];
    }
}
