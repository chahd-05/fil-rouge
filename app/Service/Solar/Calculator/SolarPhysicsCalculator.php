<?php

namespace App\Services\Solar\Calculator;

class SolarPhysicsCalculator
{
    public function calculateRequiredPvPower(float $dailyEnergy, float $dailyIrradiation, float $performanceRatio): float
    {
        return round($dailyEnergy / max($dailyIrradiation * $performanceRatio, 0.01), 2);
    }

    public function calculatePerformanceRatio(array $losses): float
    {
        $totalLossPercent = array_sum($losses);
        $ratio = 1 - ($totalLossPercent / 100);

        return round(max($ratio, 0.5), 3);
    }

    public function calculatePanelCount(float $requiredKw, float $panelPowerW): int
    {
        return (int) ceil(($requiredKw * 1000) / max($panelPowerW, 1));
    }

    public function calculateArrayArea(int $panelCount, float $panelLength, float $panelWidth): float
    {
        return round($panelCount * $panelLength * $panelWidth, 2);
    }

    public function calculateStringVoc(float $panelVoc, int $panelsPerString, float $temperatureMin, float $temperatureCoefficient): float
    {
        $delta = 25 - $temperatureMin;
        $correction = 1 + ((abs($temperatureCoefficient) / 100) * $delta);

        return round($panelVoc * $panelsPerString * $correction, 2);
    }

    public function calculateStringVmp(float $panelVmp, int $panelsPerString, float $temperatureMax, float $temperatureCoefficient): float
    {
        $delta = max($temperatureMax - 25, 0);
        $correction = 1 - ((abs($temperatureCoefficient) / 100) * $delta);

        return round($panelVmp * $panelsPerString * max($correction, 0.7), 2);
    }

    public function calculateCurrent(float $powerKw, float $voltage, float $powerFactor = 1, bool $threePhase = false): float
    {
        $divider = $threePhase ? sqrt(3) * $voltage * max($powerFactor, 0.8) : $voltage;

        return round(($powerKw * 1000) / max($divider, 1), 2);
    }

    public function calculateCableSection(float $length, float $current, float $voltageDropPercent, float $voltage, string $material = 'Copper'): float
    {
        $rho = strtolower($material) === 'aluminium' ? 0.0282 : 0.0175;
        $deltaV = ($voltageDropPercent / 100) * $voltage;

        return round((2 * $rho * $length * $current) / max($deltaV, 0.1), 2);
    }

    public function calculateAnnualProduction(
        float $pvPowerKw,
        float $irradiationYearly,
        float $performanceRatio,
        float $orientationFactor,
        float $tiltFactor
    ): float {
        $specificYield = $irradiationYearly * $performanceRatio * $orientationFactor * $tiltFactor;

        return round($pvPowerKw * $specificYield, 2);
    }

    public function calculateLoadSplit(float $dailyConsumption, float $dayPercent, float $nightPercent): array
    {
        return [
            'day_kwh' => round($dailyConsumption * ($dayPercent / 100), 2),
            'night_kwh' => round($dailyConsumption * ($nightPercent / 100), 2),
        ];
    }
}
