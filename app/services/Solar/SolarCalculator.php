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

}