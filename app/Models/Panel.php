<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    protected $fillable = [
        'project_id',
        'power',
        'quantity',
        'efficiency'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
