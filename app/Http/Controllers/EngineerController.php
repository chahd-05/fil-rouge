<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Solar\SolarCalculator;

class EngineerController extends Controller
{
    public function testCalculation(SolarCalculator $calculator)
    {
        $data = [
            'consumption' => 10,
            'irradiation' => 5.5,
            'panel_power' => 450,
            'efficiency' => 18,
            'losses' => 20
        ];

        $solar = $calculator->calculate($data);
        $requiredPower = $solar['required_power_kw'];

        $panelPower = $calculator->normalizePanelPower($data['panel_power']);

        $panels = $calculator->calculatePanelsWithStandard(
            $requiredPower,
            $panelPower
        );

      
        $realPvPower = ($panels * $panelPower) / 1000;

        $inverterRaw = $calculator->calculateInverter($realPvPower);

        $inverterStandard = $calculator->normalizeInverter(
            $inverterRaw['suggested_inverter_kw']
        );

        $validationInverter = $calculator->validateInverter(
            $realPvPower,
            $inverterStandard
        );

        $voltage = 230;
        $length = 20;
        $allowedDrop = 3;

        $current = $calculator->calculateCurrent($realPvPower, $voltage);

        $section = $calculator->calculateCableSection(
            $length,
            $current,
            $allowedDrop,
            $voltage
        );

        $voltageCheck = $calculator->validateVoltageDrop($allowedDrop);

        $standardCable = $calculator->applyStandardSizing(
            $section,
            0,
            0
        );

       
        $breaker = $calculator->calculateBreaker($current);
        $fuse = $calculator->calculateFuse($current);

        $protectionValidation = $calculator->validateProtection(
            $current,
            $breaker,
            $fuse
        );

        $standardProtection = $calculator->applyStandardSizing(
            0,
            $breaker,
            $fuse
        );

        $globalValidation = $calculator->globalValidationFull(
            $realPvPower,
            $data['consumption'],
            $validationInverter,
            $voltageCheck,
            $protectionValidation
        );
        return response()->json([
            'solar' => [
                'required_power_kw' => $requiredPower,
                'real_power_kw' => $realPvPower,
                'panel_power_w' => $panelPower,
                'number_of_panels' => $panels
            ],

            'inverter' => [
                'raw_inverter_kw' => $inverterRaw['suggested_inverter_kw'],
                'standard_inverter_kw' => $inverterStandard,
                'validation' => $validationInverter
            ],

            'cable' => [
                'current_A' => $current,
                'section_mm2' => $section,
                'standard_section_mm2' => $standardCable['cable_mm2'],
                'voltage_drop_check' => $voltageCheck
            ],

            'protection' => [
                'breaker_A' => $breaker,
                'fuse_A' => $fuse,
                'standard_breaker_A' => $standardProtection['breaker_A'],
                'standard_fuse_A' => $standardProtection['fuse_A'],
                'validation' => $protectionValidation
            ],

            'global_validation' => $globalValidation
        ]);
    }
}