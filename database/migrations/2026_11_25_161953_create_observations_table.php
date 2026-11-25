<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_spot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedTinyInteger('confidence')->default(90);
            $table->unsignedTinyInteger('health_score');
            $table->unsignedSmallInteger('detection_count')->default(1);
            $table->timestamp('observed_at');
            $table->timestamps();

            $table->index(['plant_spot_id', 'observed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};
