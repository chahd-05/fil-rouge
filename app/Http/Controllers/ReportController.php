<?php

namespace App\Http\Controllers;

use App\Services\Solar\SolarService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function generate(SolarService $solarService)
    {
        $data = [
            'consumption' => 10,
            'irradiation' => 5.5,
            'panel_power' => 450,
            'efficiency' => 18,
            'losses' => 20
        ];

        $result = $solarService->run($data);

        $pdf = Pdf::loadView('reports.solar', [
            'data' => $result
        ]);

        return $pdf->download('solar-report.pdf');
    }
}