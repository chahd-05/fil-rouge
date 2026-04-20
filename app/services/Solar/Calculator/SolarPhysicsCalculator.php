<?php

namespace App\Services\Solar\Calculator;

class SolarPhysicsCalculator
{
    public function calculatePower(array $data)
    {
        $efficiency = $data['efficiency'] / 100;
        $losses = $data['losses'] / 100;

        $requiredPower = $data['consumption'] /
            ($data['irradiation'] * $efficiency * (1 - $losses));

        return round($requiredPower, 2);
    }

    public function calculateCurrent($powerKW, $voltage)
    {
        return round(($powerKW * 1000) / $voltage, 2);
    }

    public function calculateCableSection($length, $current, $voltageDrop, $voltage)
    {
        $rho = 0.017;
        $deltaV = ($voltageDrop / 100) * $voltage;

        return round((2 * $rho * $length * $current) / $deltaV, 2);
    }
}