<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    protected $fillable = [
        'project_id',
        'model',
        'power',
        'quantity',
        'efficiency',
        'voc',
        'vmp',
        'isc',
        'imp',
        'length_m',
        'width_m',
        'temperature_coefficient',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
