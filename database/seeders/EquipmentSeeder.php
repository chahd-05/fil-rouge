<?php

namespace Database\Seeders;

use App\Models\Cable;
use App\Models\Inverter;
use App\Models\Panel;
use App\Models\Protection;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPanels();
        $this->seedInverters();
        $this->seedCables();
        $this->seedProtections();
    }

    private function seedPanels(): void
    {
        $panels = [
            [
                'model' => 'Helio Mono 450',
                'power' => 450,
                'quantity' => 1,
                'efficiency' => 20.8,
                'voc' => 49.5,
                'vmp' => 41.5,
                'isc' => 11.6,
                'imp' => 10.85,
                'length_m' => 2.1,
                'width_m' => 1.05,
                'temperature_coefficient' => -0.34,
            ],
            [
                'model' => 'Helio Mono 550',
                'power' => 550,
                'quantity' => 1,
                'efficiency' => 21.3,
                'voc' => 50.2,
                'vmp' => 42.3,
                'isc' => 13.8,
                'imp' => 13.0,
                'length_m' => 2.28,
                'width_m' => 1.13,
                'temperature_coefficient' => -0.32,
            ],
            [
                'model' => 'Helio Bifacial 610',
                'power' => 610,
                'quantity' => 1,
                'efficiency' => 22.1,
                'voc' => 52.1,
                'vmp' => 44.0,
                'isc' => 14.7,
                'imp' => 13.87,
                'length_m' => 2.38,
                'width_m' => 1.3,
                'temperature_coefficient' => -0.3,
            ],
        ];

        foreach ($panels as $panel) {
            Panel::query()->updateOrCreate(
                ['project_id' => null, 'model' => $panel['model']],
                $panel
            );
        }
    }

    private function seedInverters(): void
    {
        $inverters = [
            [
                'model' => 'SunCore 5K-TL',
                'power' => 5,
                'ac_power_kw' => 5,
                'max_dc_power_kw' => 6.5,
                'mppt_count' => 2,
                'strings_per_mppt' => 2,
                'mppt_min_voltage' => 120,
                'mppt_max_voltage' => 550,
                'max_dc_voltage' => 600,
                'max_input_current' => 28,
                'nominal_ac_voltage' => 230,
                'type' => 'String',
                'efficiency' => 97.6,
            ],
            [
                'model' => 'SunCore 12K-TL',
                'power' => 12,
                'ac_power_kw' => 12,
                'max_dc_power_kw' => 15.6,
                'mppt_count' => 2,
                'strings_per_mppt' => 2,
                'mppt_min_voltage' => 180,
                'mppt_max_voltage' => 850,
                'max_dc_voltage' => 1000,
                'max_input_current' => 32,
                'nominal_ac_voltage' => 400,
                'type' => 'On-grid',
                'efficiency' => 98.3,
            ],
            [
                'model' => 'SunCore 50K-TL',
                'power' => 50,
                'ac_power_kw' => 50,
                'max_dc_power_kw' => 65,
                'mppt_count' => 4,
                'strings_per_mppt' => 2,
                'mppt_min_voltage' => 200,
                'mppt_max_voltage' => 900,
                'max_dc_voltage' => 1100,
                'max_input_current' => 40,
                'nominal_ac_voltage' => 400,
                'type' => 'Industrial',
                'efficiency' => 98.7,
            ],
        ];

        foreach ($inverters as $inverter) {
            Inverter::query()->updateOrCreate(
                ['project_id' => null, 'model' => $inverter['model']],
                $inverter
            );
        }
    }

    private function seedCables(): void
    {
        $cables = [
            ['length' => 25, 'section' => 4, 'material' => 'Copper', 'voltage_type' => 'DC', 'ampacity' => 36, 'cost_per_meter' => 24],
            ['length' => 30, 'section' => 6, 'material' => 'Copper', 'voltage_type' => 'DC', 'ampacity' => 46, 'cost_per_meter' => 31],
            ['length' => 40, 'section' => 10, 'material' => 'Copper', 'voltage_type' => 'AC', 'ampacity' => 63, 'cost_per_meter' => 46],
            ['length' => 50, 'section' => 16, 'material' => 'Copper', 'voltage_type' => 'AC', 'ampacity' => 85, 'cost_per_meter' => 67],
        ];

        foreach ($cables as $cable) {
            Cable::query()->updateOrCreate(
                ['project_id' => null, 'section' => $cable['section'], 'voltage_type' => $cable['voltage_type']],
                $cable
            );
        }
    }

    private function seedProtections(): void
    {
        foreach (['Breaker', 'Fuse', 'SPD', 'Earthing'] as $type) {
            foreach ([16, 20, 25, 32, 40, 50, 63, 80, 100, 125] as $rating) {
                Protection::query()->firstOrCreate([
                    'project_id' => null,
                    'type' => $type,
                    'rating' => $rating,
                ]);
            }
        }
    }
}
