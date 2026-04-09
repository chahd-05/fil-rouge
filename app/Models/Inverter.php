<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inverter extends Model
{
    protected $fillable = [
        'project_id',
        'power',
        'type',
        'efficiency'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
