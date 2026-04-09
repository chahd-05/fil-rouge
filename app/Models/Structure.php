<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'layout',
        'panel_width',
        'panel_height',
        'panel_weight',
        'panel_count',
        'total_surface',
        'rail_count',
        'rail_length_total',
        'fixation_count',
        'spacing_between_fixations',
        'structure_material',
        'tilt_angle',
        'wind_load',
        'total_weight',
        'safety_factor'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
