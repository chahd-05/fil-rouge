<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Protection extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'rating'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
