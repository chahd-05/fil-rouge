<?php

namespace App\Models;

use App\Models\Battery;
use App\Models\Inverter;
use App\Models\Panel;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'location',
        'consumption',
        'surface',
        'budget',
        'user_id'
    ];

    public function panels()
    {
        return $this->hasMany(Panel::class);
    }

    public function inverter()
    {
        return $this->hasOne(Inverter::class);
    }

    public function battery()
    {
        return $this->hasOne(Battery::class);
    }

    public function cables()
    {
        return $this->hasMany(Cable::class);
    }

    public function protections()
    {
        return $this->hasMany(Protection::class);
    }

}
