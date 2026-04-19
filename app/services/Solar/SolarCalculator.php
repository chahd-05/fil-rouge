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

    public function calculateInverter($pvPowerKW) {

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

}