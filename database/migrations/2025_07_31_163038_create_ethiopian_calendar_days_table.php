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
        Schema::create('ethiopian_calendar_days', function (Blueprint $table) {
            $table->id();
            $table->date('gregorian_date');
            $table->string('ethiopian_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->string('region')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethiopian_calendar_days');
    }
};
