<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('robot_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('greenhouse_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['idle', 'scanning', 'charging', 'error'])->default('idle');
            $table->unsignedTinyInteger('battery_pct')->default(100);
            $table->timestamp('last_scan_at')->nullable();
            $table->unsignedSmallInteger('scans_completed_today')->default(0);
            $table->unsignedSmallInteger('error_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('robot_statuses');
    }
};
