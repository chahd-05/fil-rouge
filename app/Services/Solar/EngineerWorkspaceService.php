<?php

namespace App\Services\Solar;

use App\Models\Cable;
use App\Models\Inverter;
use App\Models\Panel;
use App\Models\Project;

class EngineerWorkspaceService
{
    public function __construct(
        private readonly SolarService $solarService,
    ) {
    }

    public function dashboardData(?Project $project = null, array $formData = []): array
    {
        $panelOptions = Panel::query()->whereNull('project_id')->orderByDesc('power')->get();
        $inverterOptions = Inverter::query()->whereNull('project_id')->orderBy('ac_power_kw')->get();
        $cableOptions = Cable::query()->whereNull('project_id')->orderBy('section')->get();

        $projectSnapshot = $project ? $this->solarService->projectToDashboardPayload($project) : null;

        return [
            'panelOptions' => $panelOptions,
            'inverterOptions' => $inverterOptions,
            'cableOptions' => $cableOptions,
            'recentProjects' => Project::query()->latest()->take(8)->get(),
            'project' => $project,
            'result' => $projectSnapshot['result'] ?? null,
            'comparison' => $projectSnapshot['comparison'] ?? [],
            'recommendedScenario' => $projectSnapshot['recommendedScenario'] ?? null,
            'formData' => array_merge([
                'project_id' => null,
                'project_name' => 'Casablanca Rooftop PV Study',
                'city' => 'Casablanca',
                'country' => 'Morocco',
                'daily_consumption' => 10,
                'peak_power' => 5,
                'day_usage_percent' => 60,
                'night_usage_percent' => 40,
                'autonomy_days' => 1,
                'irradiation' => null,
                'use_auto_irradiation' => 1,
                'tilt_angle' => 25,
                'azimuth' => 0,
                'available_surface' => 60,
                'mounting_type' => 'roof',
                'structure_type' => 'fixed',
                'temperature_min' => 2,
                'temperature_max' => 42,
                'cable_length' => 25,
                'price_per_kwh' => 1.2,
                'latitude' => null,
                'longitude' => null,
                'performance_ratio' => 0.8,
                'temperature_loss_percent' => 6,
                'inverter_loss_percent' => 3,
                'dust_loss_percent' => 2,
                'other_loss_percent' => 2,
                'degradation' => 0.5,
                'budget' => 0,
                'installation_type' => 'Residential',
                'panel_id' => optional($panelOptions->first())->id,
                'inverter_id' => null,
                'cable_id' => optional($cableOptions->first())->id,
            ], $projectSnapshot['formData'] ?? [], $formData),
        ];
    }

    public function calculateAndStore(array $data): Project
    {
        return $this->solarService->calculateAndStore($data);
    }

    public function preview(array $data): array
    {
        return $this->solarService->calculate($data, storeComparison: true);
    }
}
