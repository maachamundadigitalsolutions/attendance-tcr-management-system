<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user_id')->unique(); // 👈 unique user_id  // engineer nu unique ID
            $table->string('email')->nullable()->unique(); // 👈 nullable + unique, no change()
            $table->timestamp('email_verified_at')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->enum('shirt_size', ['XS','S','M','L','XL','XXL'])->nullable();
            $table->enum('tshirt_size', ['XS','S','M','L','XL','XXL'])->nullable();
            $table->enum('trouser_size', ['28','30','32','34','36','38'])->nullable();
            $table->enum('jeans_size', ['28','30','32','34','36','38', '46', '48', '50', '52'])->nullable();
            $table->date('dob')->nullable();
            $table->date('doj')->nullable();
            $table->string('education')->nullable();
            $table->string('total_exp')->nullable();
            $table->string('summary_exp')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('product')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
