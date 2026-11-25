<?php

namespace App\Models;

use Database\Factories\PlantSpotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlantSpot extends Model
{
    /** @use HasFactory<PlantSpotFactory> */
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'public_code',
        'row_no',
        'position_no',
        'planted_at',
    ];

    protected function casts(): array
    {
        return [
            'planted_at' => 'date',
        ];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class)->latest('observed_at');
    }

    public function latestObservation(): HasOne
    {
        return $this->hasOne(Observation::class)->latestOfMany('observed_at');
    }
}
