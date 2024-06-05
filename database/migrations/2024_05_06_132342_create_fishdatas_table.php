<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fishdatas', function (Blueprint $table) {
            $table->id();
            $table->string('common_name');
            $table->string('scientific_name');
            $table->string('aquarium_size');
            $table->string('habitat', 1000)->nullable();
            $table->string('max_standard_length');
            $table->string('temperature');
            $table->string('ph');
            $table->string('diet');
            $table->string('behavior');
            $table->string('sexual_dimorphisms', 500);
            $table->string('reproduction', 1000);
            $table->string('notes', 1000);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fishdatas');
    }
};
