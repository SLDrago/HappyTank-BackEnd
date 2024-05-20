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
        $table->string('Common_Name');
        $table->string('Scientific_Name');
        $table->string('Aquarium_Size');
        $table->string('Habitat', 1000)->nullable();
        $table->string('Max_Standard_length');
        $table->string('Temperature');
        $table->string('PH');
        $table->string('Diet');
        $table->string('Behavior/Compatability');
        $table->string('Sexual_Dimorphisms', 500);
        $table->string('Reproduction', 1000);
        $table->string('Notes', 1000);
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
