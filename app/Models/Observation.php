<?php

namespace App\Models;

use Database\Factories\ObservationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Observation extends Model
{
    /** @use HasFactory<ObservationFactory> */
    use HasFactory;

    protected $fillable = [
        'plant_spot_id',
        'category_id',
        'image_path',
        'confidence',
        'health_score',
        'detection_count',
        'observed_at',
    ];

    protected function casts(): array
    {
        return [
            'observed_at' => 'datetime',
        ];
    }

    public function plantSpot(): BelongsTo
    {
        return $this->belongsTo(PlantSpot::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function alert(): HasOne
    {
        return $this->hasOne(Alert::class);
    }
}
