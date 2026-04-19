<?php

namespace App\Services\Solar;

class SolarCalculator
{
    public function calculate($data)
    {
        $consumption = $data['consumption']; 
        $irradiation = $data['irradiation']; 
        $panelPower = $data['panel_power']; 
        $efficiency = $data['efficiency']; 
        $losses = $data['losses']; 
        
        $efficiency = $efficiency / 100;
        $losses = $losses / 100;

        $requiredPower = $consumption / ($irradiation * $efficiency * (1 - $losses));

        $panelPowerKW = $panelPower / 1000;
        $numberOfPanels = ceil($requiredPower / $panelPowerKW);

        return [
            'required_power_kw' => round($requiredPower, 2),
            'number_of_panels' => $numberOfPanels
        ];
    }

    public function calculateInverter($pvPowerKW)
    {
        $suggestedInverter = round($pvPowerKW * 0.9, 2);

        return [
            'suggested_inverter_kw' => $suggestedInverter
        ];
    }

    public function validateInverter($pvPowerKW, $inverterPowerKW)
    {
        $ratio = $inverterPowerKW / $pvPowerKW;

        if ($ratio < 0.8) {
            return [
                'status' => 'under-sized',
                'message' => 'Inverter too small'
            ];
        }

        if ($ratio > 1.2) {
            return [
                'status' => 'over-sized',
                'message' => 'Inverter too large'
            ];
        }

        return [
            'status' => 'valid',
            'message' => 'Inverter is compatible'
        ];
    }

    public function calculateCurrent($powerKW, $voltage)
    {
        $powerW = $powerKW * 1000;
        return round($powerW / $voltage, 2);
    }

    public function calculateCableSection($length, $current, $voltageDrop, $voltage)
    {
        $rho = 0.017;

        $deltaV = ($voltageDrop / 100) * $voltage;

        $section = (2 * $rho * $length * $current) / $deltaV;

        return round($section, 2);
    }

    public function validateVoltageDrop($voltageDrop)
    {
        if ($voltageDrop > 5) {
            return [
                'status' => 'danger',
                'message' => 'Voltage drop too high'
            ];
        }

        if ($voltageDrop > 3) {
            return [
                'status' => 'warning',
                'message' => 'Voltage drop acceptable'
            ];
        }

        return [
            'status' => 'good',
            'message' => 'Voltage drop is good'
        ];
    }

    public function calculateBreaker($current)
    {
        $breaker = $current * 1.25;

        return round($breaker, 2);
    }

    public function calculateFuse($current)
    {
        $fuse = $current * 1.2;

        return round($fuse, 2);
    }

    public function validateProtection($current, $breaker, $fuse)
    {
        if ($breaker < $current || $fuse < $current) {
            return [
                'status' => 'danger',
                'message' => 'Protection insufficient'
            ];
        }

        return [
            'status' => 'safe',
            'message' => 'Protection is adequate'
        ];
    }

    public function normalizePanelPower($power)
    {
        $standards = [300, 350, 400, 450, 500, 550];

        return $this->getStandardValue($power, $standards);
    }

    public function calculatePanelsWithStandard($requiredPowerKW, $panelPowerW)
    {
        $panelKW = $panelPowerW / 1000;

        return ceil($requiredPowerKW / $panelKW);
    }

    public function normalizeInverter($power)
    {
        $standards = [1, 2, 3, 5, 6, 8, 10, 12, 15, 20];

        return $this->getStandardValue($power, $standards);
    }

    public function applyStandardSizing($section, $breaker, $fuse)
{
    $cableStandards = [1.5, 2.5, 4, 6, 10, 16, 25, 35, 50, 70, 95, 120, 150];
    $protectionStandards = [6, 10, 16, 20, 25, 32, 40, 50, 63, 80, 100, 125, 160];

    return [
        'cable_mm2' => $this->getStandardValue($section, $cableStandards),
        'breaker_A' => $this->getStandardValue($breaker, $protectionStandards),
        'fuse_A' => $this->getStandardValue($fuse, $protectionStandards),
    ];
}

    public function globalValidationFull($realPvPower, $consumption, $inverterValidation, $voltageCheck, $protectionValidation)
    {
        if ($realPvPower < $consumption) {
            return [
                'status' => 'under-sized',
                'message' => 'System does not cover consumption'
            ];
        }

        if (
            $inverterValidation['status'] !== 'valid' ||
            $voltageCheck['status'] === 'danger' ||
            $protectionValidation['status'] === 'danger'
        ) {
            return [
                'status' => 'rejected',
                'message' => 'System has technical issues'
            ];
        }

        return [
            'status' => 'approved',
            'message' => 'Full system is valid and optimized'
        ];
    }

    public function getStandardValue($value, $standards)
    {
        foreach ($standards as $standard) {
            if ($standard >= $value) {
                return $standard;
            }
        }

        return end($standards);
    }
}