<?php

namespace App\Services\Solar;

use App\Services\Solar\Calculator\SolarPhysicsCalculator;
use App\Services\Solar\Standards\SolarStandards;
use App\Services\Solar\Validators\SolarValidator;
use App\Services\Solar\Structure\StructureCalculator;
use App\Services\Solar\Structure\MassifCalculator;
use App\Services\Solar\Finance\CostCalculator;
use App\Services\Solar\Simulation\ProductionSimulator;

class SolarService
{
private CostCalculator $cost;

private ProductionSimulator $simulator;

public function __construct(
    SolarPhysicsCalculator $calc,
    SolarStandards $std,
    SolarValidator $val,
    StructureCalculator $structure,
    MassifCalculator $massif,
    CostCalculator $cost,
    ProductionSimulator $simulator
) {
    $this->calc = $calc;
    $this->std = $std;
    $this->val = $val;
    $this->structure = $structure;
    $this->massif = $massif;
    $this->cost = $cost;
    $this->simulator = $simulator;
}

    public function run(array $data)
    {
        // 1. Solar
        $required = $this->calc->calculatePower($data);

        $panel = $this->std->getStandardValue(
            $data['panel_power'],
            $this->std->panelStandards()
        );

        $panels = ceil($required / ($panel / 1000));

        $realPv = ($panels * $panel) / 1000;
        // PRODUCTION SIMULATION
        $monthlyProduction = $this->simulator->monthlyProduction($realPv);

        $yearlyProduction = $this->simulator->yearlyProduction($monthlyProduction);

        // STRUCTURE
        $structureData = $this->structure->calculateStructure($panels);

        $massifData = $this->massif->calculateMassifs(
        $panels,
        $structureData['rows']
        );

        $concrete = $this->massif->concreteVolume(
        $massifData['massifs_count']
        );

        // 2. Inverter
        $inverterRaw = $realPv * 0.9;

        $inverter = $this->std->getStandardValue(
            $inverterRaw,
            $this->std->inverterStandards()
        );

        $invVal = $this->val->inverter($realPv, $inverter);

        // 3. Cable
        $voltage = 230;
        $current = $this->calc->calculateCurrent($realPv, $voltage);

        $section = $this->calc->calculateCableSection(
            20,
            $current,
            3,
            $voltage
        );

        $voltageCheck = $this->val->voltageDrop(3);

        $standardCable = $this->std->getStandardValue(
            $section,
            $this->std->cableStandards()
        );

        // 4. Protection
        $breaker = $current * 1.25;
        $fuse = $current * 1.2;

        $protectionVal = $this->val->protection($current, $breaker, $fuse);

        $standardProtection = $this->std->getStandardValue(
            $breaker,
            $this->std->protectionStandards()
        );

    // COSTS
        $panelCost = $this->cost->calculatePanelCost($panels);
        $inverterCost = $this->cost->calculateInverterCost($inverter);
        $cableCost = $this->cost->calculateCableCost();
        $structureCost = $this->cost->calculateStructureCost($panels);
        $massifCost = $this->cost->calculateMassifCost($concrete);

        $materialTotal = $this->cost->totalCost([
        $panelCost,
        $inverterCost,
        $cableCost,
        $structureCost,
        $massifCost
    ]);

    $installation = $this->cost->calculateInstallationCost($materialTotal);

    $total = $materialTotal + $installation;

// SIMPLE production estimation
    $annualProduction = $realPv * 365;

// ROI
    $roi = $this->cost->calculateROI($total, $annualProduction);

        // 5. Global
        $global = $this->val->global(
            $realPv,
            $data['consumption'],
            $invVal,
            $voltageCheck,
            $protectionVal
        );

        return [
            'solar' => [
                'required_kw' => $required,
                'real_kw' => $realPv,
                'panels' => $panels
            ],
            'inverter' => [
                'value' => $inverter,
                'validation' => $invVal
            ],
            'cable' => [
                'section' => $section,
                'standard' => $standardCable
            ],
            'protection' => [
                'breaker' => $breaker,
                'fuse' => $fuse,
                'standard' => $standardProtection
            ],
            'structure' => [
                'rows' => $structureData['rows'],
                'panels_per_row' => $structureData['panels_per_row'],
                'total_length_m' => $structureData['total_length_m'],
                'total_width_m' => $structureData['total_width_m']
            ],

            'massifs' => [
                'count' => $massifData['massifs_count'],
                'concrete_volume_m3' => $concrete
            ],
            'costs' => [
                'panels' => $panelCost,
                'inverter' => $inverterCost,
                'cable' => $cableCost,
                'structure' => $structureCost,
                'massifs' => $massifCost,
                'installation' => $installation,
                'total' => $total,
                'roi_years' => $roi
            ],
            'production' => [
            'monthly_kwh' => $monthlyProduction,
            'yearly_kwh' => $yearlyProduction
            ],
            'global' => $global
        ];
    }
}