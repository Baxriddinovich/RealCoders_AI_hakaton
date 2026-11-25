<?php

namespace App\Models;

use Database\Factories\RobotStatusFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RobotStatus extends Model
{
    /** @use HasFactory<RobotStatusFactory> */
    use HasFactory;

    protected $fillable = [
        'greenhouse_id',
        'status',
        'battery_pct',
        'last_scan_at',
        'scans_completed_today',
        'error_count',
    ];

    protected function casts(): array
    {
        return [
            'last_scan_at' => 'datetime',
        ];
    }

    public function greenhouse(): BelongsTo
    {
        return $this->belongsTo(Greenhouse::class);
    }
}
