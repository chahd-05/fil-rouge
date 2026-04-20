<?php

namespace App\Services\Solar\Finance;

class CostCalculator
{
    public function calculatePanelCost($panels, $pricePerPanel = 1500)
    {
        return $panels * $pricePerPanel;
    }

    public function calculateInverterCost($powerKW)
    {
        // average 1000 MAD per kW
        return $powerKW * 1000;
    }

    public function calculateCableCost($length = 20, $pricePerMeter = 20)
    {
        return $length * $pricePerMeter;
    }

    public function calculateStructureCost($panels)
    {
        // estimated structure cost per panel
        return $panels * 300;
    }

    public function calculateMassifCost($concreteVolume, $pricePerM3 = 800)
    {
        return $concreteVolume * $pricePerM3;
    }

    public function calculateInstallationCost($totalMaterialCost)
    {
        // 10% labor
        return $totalMaterialCost * 0.1;
    }

    public function totalCost($costs)
    {
        return array_sum($costs);
    }

    public function calculateROI($totalCost, $annualProductionKWh, $pricePerKWh = 1.2)
    {
        $annualRevenue = $annualProductionKWh * $pricePerKWh;

        if ($annualRevenue == 0) return 0;

        return round($totalCost / $annualRevenue, 1); // years
    }
}