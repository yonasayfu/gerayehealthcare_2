<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_delegations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('assigned_to')->constrained('staff')->cascadeOnDelete();
            $table->date('due_date');
            $table->enum('status', ['Pending', 'In Progress', 'Completed'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_delegations');
    }
};
