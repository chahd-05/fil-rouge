<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Solar\SolarService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function generate(SolarService $solarService): Response
    {
        $project = $solarService->calculateAndStore([
            'project_name' => 'Demo Solar Study',
            'city' => 'Casablanca',
            'country' => 'Morocco',
            'installation_type' => 'Residential',
            'daily_consumption' => 10,
            'peak_power' => 5,
            'day_usage_percent' => 60,
            'night_usage_percent' => 40,
            'autonomy_days' => 1,
            'tilt_angle' => 25,
            'azimuth' => 0,
            'available_surface' => 60,
            'mounting_type' => 'roof',
            'structure_type' => 'fixed',
            'temperature_min' => 5,
            'temperature_max' => 42,
            'cable_length' => 25,
            'price_per_kwh' => 1.2,
            'performance_ratio' => 0.8,
            'temperature_loss_percent' => 6,
            'inverter_loss_percent' => 3,
            'dust_loss_percent' => 2,
            'other_loss_percent' => 2,
            'degradation' => 0.5,
            'panel_id' => \App\Models\Panel::query()->whereNull('project_id')->value('id'),
        ]);

        return $this->downloadProjectReport($project, $solarService);
    }

    public function project(Project $project, SolarService $solarService): Response
    {
        return $this->downloadProjectReport($project, $solarService);
    }

    private function downloadProjectReport(Project $project, SolarService $solarService): Response
    {
        $payload = $solarService->projectToDashboardPayload($project);
        $pdf = Pdf::loadView('reports.solar', [
            'project' => $project,
            'data' => $payload['result'],
            'comparison' => $payload['comparison'],
            'recommendedScenario' => $payload['recommendedScenario'],
        ]);

        return $pdf->download('solar-project-' . $project->id . '.pdf');
    }
}
