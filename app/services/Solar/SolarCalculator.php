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

   
}