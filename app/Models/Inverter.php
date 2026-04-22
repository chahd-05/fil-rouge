<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inverter extends Model
{
    protected $fillable = [
        'project_id',
        'model',
        'power',
        'ac_power_kw',
        'max_dc_power_kw',
        'mppt_count',
        'strings_per_mppt',
        'mppt_min_voltage',
        'mppt_max_voltage',
        'max_dc_voltage',
        'max_input_current',
        'nominal_ac_voltage',
        'type',
        'efficiency',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
