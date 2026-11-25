<?php

namespace App\Models;

use Database\Factories\YieldForecastFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YieldForecast extends Model
{
    /** @use HasFactory<YieldForecastFactory> */
    use HasFactory;

    protected $fillable = [
        'greenhouse_id',
        'forecast_date',
        'horizon_days',
        'expected_kg',
        'grade_a_pct',
        'grade_b_pct',
        'grade_c_pct',
    ];

    protected function casts(): array
    {
        return [
            'forecast_date' => 'date',
        ];
    }

    public function greenhouse(): BelongsTo
    {
        return $this->belongsTo(Greenhouse::class);
    }
}
