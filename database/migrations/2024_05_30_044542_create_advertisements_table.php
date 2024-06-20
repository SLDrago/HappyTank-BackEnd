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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('small_description');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('price_based_on');
            $table->unsignedBigInteger('category_id');
            $table->boolean('status')->default(true);
            $table->string('tags');
            $table->unsignedBigInteger('views')->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index('category_id');
            $table->index('status');
            $table->index('user_id');
            $table->index('price');
            $table->index('tags');
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
