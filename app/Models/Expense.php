<?php

namespace App\Models;

use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    public const CATEGORIES = [
        'gaz' => 'Gaz',
        'svet' => 'Svet (elektr)',
        'suv' => 'Suv',
        'ishchi_kuchi' => 'Ishchi kuchi',
        'urug_kochat' => "Urug'/ko'chat",
        'ogit' => "O'g'it",
        'dori' => 'Dori-darmon',
        'texnika' => 'Texnika/ta\'mirlash',
        'boshqa' => 'Boshqa',
    ];

    protected $fillable = [
        'greenhouse_id',
        'category',
        'amount',
        'spent_on',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'spent_on' => 'date',
        ];
    }

    public function greenhouse(): BelongsTo
    {
        return $this->belongsTo(Greenhouse::class);
    }
}
