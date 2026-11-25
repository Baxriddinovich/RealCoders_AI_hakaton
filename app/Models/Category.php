<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_uz',
        'name_en',
        'type',
        'severity_weight',
        'color',
    ];

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }
}
