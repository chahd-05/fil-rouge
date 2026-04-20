<?php

namespace App\Services\Solar\Simulation;

class ProductionSimulator
{
    public function monthlyProduction($pvPowerKW)
    {
        // irradiation coefficient per month (simplified for Morocco)
        $irradiation = [
            0.75, // Jan
            0.85, // Feb
            1.0,  // Mar
            1.1,  // Apr
            1.2,  // May
            1.3,  // Jun
            1.35, // Jul
            1.3,  // Aug
            1.15, // Sep
            1.0,  // Oct
            0.85, // Nov
            0.75  // Dec
        ];

        $monthly = [];

        foreach ($irradiation as $index => $factor) {
            $production = $pvPowerKW * 30 * $factor;
            $monthly[] = round($production, 2);
        }

        return $monthly;
    }

    public function yearlyProduction($monthlyData)
    {
        return array_sum($monthlyData);
    }
}