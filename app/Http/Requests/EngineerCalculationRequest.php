<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EngineerCalculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'project_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'installation_type' => ['required', 'in:Residential,Industrial'],
            'daily_consumption' => ['required', 'numeric', 'min:0.1'],
            'peak_power' => ['required', 'numeric', 'min:0.1'],
            'day_usage_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'night_usage_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'autonomy_days' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'irradiation' => ['nullable', 'numeric', 'min:0.5', 'max:4000'],
            'use_auto_irradiation' => ['nullable', 'boolean'],
            'tilt_angle' => ['required', 'numeric', 'min:0', 'max:90'],
            'azimuth' => ['required', 'numeric', 'min:-180', 'max:180'],
            'available_surface' => ['required', 'numeric', 'min:1'],
            'mounting_type' => ['required', 'in:roof,ground'],
            'structure_type' => ['required', 'in:fixed,tracking'],
            'temperature_min' => ['required', 'numeric', 'min:-50', 'max:80'],
            'temperature_max' => ['required', 'numeric', 'min:-20', 'max:100'],
            'cable_length' => ['required', 'numeric', 'min:1', 'max:1000'],
            'price_per_kwh' => ['required', 'numeric', 'min:0.01'],
            'panel_id' => ['nullable', 'exists:panels,id'],
            'inverter_id' => ['nullable', 'exists:inverters,id'],
            'cable_id' => ['nullable', 'exists:cables,id'],
            'performance_ratio' => ['required', 'numeric', 'min:0.5', 'max:0.95'],
            'temperature_loss_percent' => ['required', 'numeric', 'min:0', 'max:30'],
            'inverter_loss_percent' => ['required', 'numeric', 'min:0', 'max:20'],
            'dust_loss_percent' => ['required', 'numeric', 'min:0', 'max:20'],
            'other_loss_percent' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'degradation' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'budget' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $day = (float) $this->input('day_usage_percent', 0);
            $night = (float) $this->input('night_usage_percent', 0);

            if (abs(($day + $night) - 100) > 0.01) {
                $validator->errors()->add('night_usage_percent', 'Day and night usage percentages must total 100%.');
            }
        });
    }
}
