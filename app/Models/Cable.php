<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cable extends Model
{
    protected $fillable = [
        'project_id',
        'length',
        'section',
        'material'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
