<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yield_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('greenhouse_id')->constrained()->cascadeOnDelete();
            $table->date('forecast_date');
            $table->unsignedSmallInteger('horizon_days');
            $table->decimal('expected_kg', 8, 2);
            $table->decimal('grade_a_pct', 5, 2);
            $table->decimal('grade_b_pct', 5, 2);
            $table->decimal('grade_c_pct', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yield_forecasts');
    }
};
