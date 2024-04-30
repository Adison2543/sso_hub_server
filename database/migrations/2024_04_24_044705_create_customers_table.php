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
        Schema::create('customers', function (Blueprint $table) {
            $table->char('citizen_id', 13)->unique()->primary();
            $table->string('password');
            $table->string('full_name');
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('province')->nullable();
            $table->string('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('course');
            $table->string('life_time')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
