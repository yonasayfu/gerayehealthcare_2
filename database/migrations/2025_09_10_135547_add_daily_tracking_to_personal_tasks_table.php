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
        Schema::table('personal_tasks', function (Blueprint $table) {
            // Add fields for daily task tracking and performance analysis
            $table->date('task_date')->nullable()->after('my_day_for');
            $table->time('start_time')->nullable()->after('task_date');
            $table->time('end_time')->nullable()->after('start_time');
            $table->integer('estimated_duration_minutes')->nullable()->after('end_time');
            $table->text('daily_notes')->nullable()->after('estimated_duration_minutes');
            $table->string('task_category')->nullable()->after('daily_notes');
            $table->integer('priority_level')->default(1)->after('task_category'); // 1-5 scale
            $table->boolean('is_billable')->default(false)->after('priority_level');

            // Indexes for performance
            $table->index(['user_id', 'task_date']);
            $table->index(['task_date', 'is_completed']);
            $table->index(['user_id', 'task_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'task_date']);
            $table->dropIndex(['task_date', 'is_completed']);
            $table->dropIndex(['user_id', 'task_category']);

            $table->dropColumn([
                'task_date',
                'start_time',
                'end_time',
                'estimated_duration_minutes',
                'daily_notes',
                'task_category',
                'priority_level',
                'is_billable',
            ]);
        });
    }
};
