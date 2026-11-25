<?php

namespace App\Models;

use Database\Factories\GreenhouseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Greenhouse extends Model
{
    /** @use HasFactory<GreenhouseFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'location',
        'area_ha',
        'crop',
        'cover_type',
    ];

    protected function casts(): array
    {
        return [
            'area_ha' => 'decimal:2',
        ];
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function yieldForecasts(): HasMany
    {
        return $this->hasMany(YieldForecast::class);
    }

    public function robotStatuses(): HasMany
    {
        return $this->hasMany(RobotStatus::class);
    }
}
