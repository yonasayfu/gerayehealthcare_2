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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->boolean('is_free_service')->default(false);
            $table->string('broadcast_status')->default('Draft');
            $table->boolean('is_pagume_campaign')->default(false);
            $table->string('location')->nullable();
            $table->string('region')->nullable();
            $table->string('woreda')->nullable();
            $table->foreignId('created_by_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
