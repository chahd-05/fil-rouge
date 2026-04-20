<?php

namespace App\Http\Controllers;

use App\Services\Solar\SolarService;

class EngineerController extends Controller
{
    public function dashboard(SolarService $solarService)
    {
        $data = [
            'consumption' => 10,
            'irradiation' => 5.5,
            'panel_power' => 450,
            'efficiency' => 18,
            'losses' => 20
        ];

        $result = $solarService->run($data);

    return view('engineer.EngineerDashboard', [
        'data' => $result,
        'totalCost' => $result['costs']['total'],
        'yearlyProduction' => $result['production']['yearly_kwh']
    ]);
    }
}