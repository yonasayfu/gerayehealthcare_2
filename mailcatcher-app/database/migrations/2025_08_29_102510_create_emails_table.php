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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->json('from');
            $table->json('to');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('subject')->nullable();
            $table->longText('html_body')->nullable();
            $table->longText('text_body')->nullable();
            $table->longText('raw_source');
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};