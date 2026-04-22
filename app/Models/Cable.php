<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cable extends Model
{
    protected $fillable = [
        'project_id',
        'length',
        'section',
        'material',
        'voltage_type',
        'ampacity',
        'cost_per_meter',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
