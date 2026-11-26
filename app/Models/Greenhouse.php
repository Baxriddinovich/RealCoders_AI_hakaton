<?php

namespace App\Models;

use Database\Factories\GreenhouseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function robotStatuses(): HasManyThrough
    {
        return $this->hasManyThrough(RobotStatus::class, Zone::class);
    }
}
