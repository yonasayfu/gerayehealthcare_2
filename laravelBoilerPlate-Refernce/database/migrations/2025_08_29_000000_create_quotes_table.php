<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('text');
            $table->string('language', 10)->nullable();
            $table->boolean('pinned')->default(false);
            $table->unsignedInteger('priority')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'pinned']);
            $table->index(['user_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
