<?php

namespace App\Services\Solar\Finance;

class CostCalculator
{
    public function calculatePanelCost(int $panels, float $panelPowerW): float
    {
        $pricePerWp = $panelPowerW >= 600 ? 4.6 : ($panelPowerW >= 540 ? 4.9 : 5.2);

        return round($panels * $panelPowerW * $pricePerWp / 1000, 2);
    }

    public function calculateInverterCost(float $inverterPowerKw, int $inverterCount, string $type = 'String'): float
    {
        $multiplier = match (strtolower($type)) {
            'industrial' => 1250,
            'hybrid' => 1650,
            default => 1350,
        };

        return round($inverterPowerKw * $multiplier * $inverterCount, 2);
    }

    public function calculateCableCost(float $length, float $costPerMeter = 28, int $runs = 1): float
    {
        return round($length * $costPerMeter * $runs, 2);
    }

    public function calculateStructureCost(float $realPvKw, string $mountingType, string $structureType): float
    {
        $mountingFactor = $mountingType === 'ground' ? 1550 : 980;
        $trackingPremium = $structureType === 'tracking' ? 1.28 : 1;

        return round($realPvKw * $mountingFactor * $trackingPremium, 2);
    }

    public function calculateMassifCost(float $concreteVolume, float $pricePerM3 = 950): float
    {
        return round($concreteVolume * $pricePerM3, 2);
    }

    public function calculateProtectionCost(array $ratings): float
    {
        return round(
            ($ratings['breaker'] * 7.5) +
            ($ratings['fuse'] * 4.2) +
            ($ratings['spd'] * 5.3) +
            ($ratings['earthing'] * 2.8),
            2
        );
    }

    public function calculateInstallationCost(float $materialTotal, string $installationType): float
    {
        $factor = $installationType === 'Industrial' ? 0.16 : 0.13;

        return round($materialTotal * $factor, 2);
    }

    public function totalCost(array $costs): float
    {
        return round(array_sum($costs), 2);
    }

    public function calculateROI(float $totalCost, float $annualSavings): float
    {
        if ($annualSavings <= 0) {
            return 0;
        }

        return round($totalCost / $annualSavings, 1);
    }
}
