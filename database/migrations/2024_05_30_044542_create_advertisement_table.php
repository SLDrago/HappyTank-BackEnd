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
        Schema::create('advertisement', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('small_description');
            $table->text('description');
            $table->string('image_url_1');
            $table->string('image_url_2');
            $table->string('image_url_3')->nullable();
            $table->string('image_url_4')->nullable();
            $table->string('image_url_5')->nullable();
            $table->string('price');
            $table->string('price_based_on');
            $table->string('phone_number');
            $table->string('email');
            $table->string('category');
            $table->boolean('status');
            $table->string('tags');
            $table->unsignedBigInteger('views');
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shop');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement');
    }
};
