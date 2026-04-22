<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'location',
        'consumption',
        'surface',
        'budget',
        'user_id',
        'city',
        'input_hash',
        'input_data',
        'required_kw',
        'real_kw',
        'panels',
        'production',
        'costs',
        'roi_years',
    ];

    protected $casts = [
        'input_data' => 'array',
        'production' => 'array',
        'costs' => 'array',
    ];

    public function scopeSearch($query, ?string $search)
    {
        return $query->when($search, function ($builder, $value) {
            $builder->where('city', 'like', '%' . $value . '%');
        });
    }
}
