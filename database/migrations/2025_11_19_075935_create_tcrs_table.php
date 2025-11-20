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
        Schema::create('tcrs', function (Blueprint $table) {
            $table->id();
            $table->integer('tcr_no'); // Admin assigns only TCR No
            $table->string('sr_no')->nullable(); // Employee enters Service Order No later
            $table->unsignedBigInteger('user_id'); // Assigned employee
            $table->enum('payment_term',['case','online'])->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('tcr_photo')->nullable();
            $table->string('payment_screenshot')->nullable();
            $table->enum('status',['assigned','used','verified','rejected'])->default('assigned');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tcrs');
    }
};
