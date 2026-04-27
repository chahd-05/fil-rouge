<?php

namespace App\Services\Solar\Simulation;

class ProductionSimulator
{
    public function monthlyProduction(
        float $annualProduction,
        float $dayUsagePercent = 60,
        string $structureType = 'fixed'
    ): array {
        $distribution = [
            0.06, 0.065, 0.078, 0.087, 0.096, 0.1,
            0.104, 0.101, 0.091, 0.079, 0.071, 0.068,
        ];

        if ($structureType === 'tracking') {
            $distribution = array_map(fn (float $value) => $value * 1.04, $distribution);
        }

        $sum = array_sum($distribution);
        $daylightBias = 0.92 + (($dayUsagePercent / 100) * 0.18);
        $monthly = array_map(function (float $weight) use ($annualProduction, $sum, $daylightBias): float {
            $base = $annualProduction * ($weight / $sum);

            return round($base * $daylightBias, 2);
        }, $distribution);
        $normalizedTotal = array_sum($monthly);

        if ($normalizedTotal <= 0) {
            return $monthly;
        }

        return array_map(function (float $value) use ($annualProduction, $normalizedTotal): float {
            return round($value * ($annualProduction / $normalizedTotal), 2);
        }, $monthly);
    }

    public function yearlyProduction(array $monthlyData): float
    {
        return round(array_sum($monthlyData), 2);
    }
}
