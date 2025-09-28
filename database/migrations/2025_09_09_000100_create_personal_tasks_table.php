<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('personal_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_important')->default(false);
            $table->dateTime('reminder_at')->nullable();
            $table->dateTime('reminded_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'is_completed']);
            $table->index(['user_id', 'is_important']);
            $table->index(['user_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_tasks');
    }
};

