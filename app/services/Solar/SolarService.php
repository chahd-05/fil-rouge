<?php

namespace App\Services\Solar;

use App\Services\Solar\Calculator\SolarPhysicsCalculator;
use App\Services\Solar\Standards\SolarStandards;
use App\Services\Solar\Validators\SolarValidator;
use App\Services\Solar\Structure\StructureCalculator;
use App\Services\Solar\Structure\MassifCalculator;

class SolarService
{
   public function __construct(
    private SolarPhysicsCalculator $calc,
    private SolarStandards $std,
    private SolarValidator $val,
    private StructureCalculator $structure,
    private MassifCalculator $massif
) {}

    public function run(array $data)
    {
        // 1. Solar
        $required = $this->calc->calculatePower($data);

        $panel = $this->std->getStandardValue(
            $data['panel_power'],
            $this->std->panelStandards()
        );

        $panels = ceil($required / ($panel / 1000));

        $realPv = ($panels * $panel) / 1000;
        // STRUCTURE
        $structureData = $this->structure->calculateStructure($panels);

        $massifData = $this->massif->calculateMassifs(
        $panels,
        $structureData['rows']
        );

        $concrete = $this->massif->concreteVolume(
        $massifData['massifs_count']
        );

        // 2. Inverter
        $inverterRaw = $realPv * 0.9;

        $inverter = $this->std->getStandardValue(
            $inverterRaw,
            $this->std->inverterStandards()
        );

        $invVal = $this->val->inverter($realPv, $inverter);

        // 3. Cable
        $voltage = 230;
        $current = $this->calc->calculateCurrent($realPv, $voltage);

        $section = $this->calc->calculateCableSection(
            20,
            $current,
            3,
            $voltage
        );

        $voltageCheck = $this->val->voltageDrop(3);

        $standardCable = $this->std->getStandardValue(
            $section,
            $this->std->cableStandards()
        );

        // 4. Protection
        $breaker = $current * 1.25;
        $fuse = $current * 1.2;

        $protectionVal = $this->val->protection($current, $breaker, $fuse);

        $standardProtection = $this->std->getStandardValue(
            $breaker,
            $this->std->protectionStandards()
        );

        // 5. Global
        $global = $this->val->global(
            $realPv,
            $data['consumption'],
            $invVal,
            $voltageCheck,
            $protectionVal
        );

        return [
            'solar' => [
                'required_kw' => $required,
                'real_kw' => $realPv,
                'panels' => $panels
            ],
            'inverter' => [
                'value' => $inverter,
                'validation' => $invVal
            ],
            'cable' => [
                'section' => $section,
                'standard' => $standardCable
            ],
            'protection' => [
                'breaker' => $breaker,
                'fuse' => $fuse,
                'standard' => $standardProtection
            ],
            'structure' => [
                'rows' => $structureData['rows'],
                'panels_per_row' => $structureData['panels_per_row'],
                'total_length_m' => $structureData['total_length_m'],
                'total_width_m' => $structureData['total_width_m']
            ],

            'massifs' => [
                'count' => $massifData['massifs_count'],
                'concrete_volume_m3' => $concrete
            ],
            'global' => $global
        ];
    }
}