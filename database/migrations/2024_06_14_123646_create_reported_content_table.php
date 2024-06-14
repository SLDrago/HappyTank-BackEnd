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
        Schema::create('reported_content', function (Blueprint $table) {
            $table->id();
            $table->enum('content_type', ['Advertisement', 'Comment', 'Review', 'User']);
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('reporter_id');
            $table->string('report_reason', 255);
            $table->timestamp('report_date')->useCurrent();
            $table->enum('status', ['Pending', 'Reviewed', 'Resolved'])->default('Pending');
            $table->timestamp('review_date')->nullable();
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->foreign('reporter_id')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reported_content');
    }
};
