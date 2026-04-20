<?php

namespace App\Services\Solar\Standards;

class SolarStandards
{
    public function cableStandards()
    {
        return [1.5, 2.5, 4, 6, 10, 16, 25, 35, 50, 70, 95, 120, 150];
    }

    public function protectionStandards()
    {
        return [6, 10, 16, 20, 25, 32, 40, 50, 63, 80, 100, 125, 160];
    }

    public function inverterStandards()
    {
        return [1, 2, 3, 5, 6, 8, 10, 12, 15, 20];
    }

    public function panelStandards()
    {
        return [300, 350, 400, 450, 500, 550];
    }

    public function getStandardValue($value, array $standards)
    {
        foreach ($standards as $standard) {
            if ($standard >= $value) {
                return $standard;
            }
        }

        return end($standards);
    }
}