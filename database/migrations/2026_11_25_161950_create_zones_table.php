<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('greenhouse_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->unsignedSmallInteger('row_count')->default(10);
            $table->unsignedSmallInteger('position_count')->default(10);
            $table->timestamps();

            $table->unique(['greenhouse_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
