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
        Schema::create('shop_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('owner_name');
            $table->string('description')->nullable();
            $table->string('phone_number');
            $table->string('address');
            $table->unsignedBigInteger('city_id');
            $table->json('gps_coordinates');
            $table->json('working_hours');
            $table->json('socialmedia_links')->nullable();
            $table->timestamps();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_info');
    }
};
