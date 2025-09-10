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
        Schema::table('task_delegations', function (Blueprint $table) {
            // Add fields for daily task tracking and performance analysis
            $table->date('task_date')->nullable()->after('due_date');
            $table->time('start_time')->nullable()->after('task_date');
            $table->time('end_time')->nullable()->after('start_time');
            $table->integer('estimated_duration_minutes')->nullable()->after('end_time');
            $table->text('daily_notes')->nullable()->after('estimated_duration_minutes');
            $table->string('task_category')->nullable()->after('daily_notes');
            $table->integer('priority_level')->default(1)->after('task_category'); // 1-5 scale
            $table->boolean('is_billable')->default(false)->after('priority_level');
            $table->string('progress_status')->default('not_started')->after('is_billable'); // not_started, in_progress, completed, blocked
            
            // Indexes for performance
            $table->index(['assigned_to', 'task_date']);
            $table->index(['task_date', 'status']);
            $table->index(['assigned_to', 'task_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_delegations', function (Blueprint $table) {
            $table->dropIndex(['assigned_to', 'task_date']);
            $table->dropIndex(['task_date', 'status']);
            $table->dropIndex(['assigned_to', 'task_category']);
            
            $table->dropColumn([
                'task_date',
                'start_time',
                'end_time',
                'estimated_duration_minutes',
                'daily_notes',
                'task_category',
                'priority_level',
                'is_billable',
                'progress_status'
            ]);
        });
    }
};