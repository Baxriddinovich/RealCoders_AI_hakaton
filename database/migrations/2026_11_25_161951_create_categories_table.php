<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_uz');
            $table->string('name_en');
            $table->enum('type', ['healthy', 'disease', 'pest', 'deficiency']);
            $table->unsignedTinyInteger('severity_weight')->default(0);
            $table->string('color', 7)->default('#2E9E44');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
