<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_tasks', 'my_day_for')) {
                $table->date('my_day_for')->nullable()->after('reminded_at');
            }
            if (!Schema::hasColumn('personal_tasks', 'recurrence_type')) {
                $table->string('recurrence_type')->default('none')->after('my_day_for'); // none|daily|weekly
            }
            if (!Schema::hasColumn('personal_tasks', 'recurrence_weekdays')) {
                $table->json('recurrence_weekdays')->nullable()->after('recurrence_type'); // [0..6]
            }
        });

        if (!Schema::hasTable('personal_task_subtasks')) {
            Schema::create('personal_task_subtasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('personal_task_id')->constrained('personal_tasks')->cascadeOnDelete();
                $table->string('title');
                $table->boolean('is_completed')->default(false);
                $table->unsignedInteger('position')->default(0);
                $table->timestamps();
                $table->index(['personal_task_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('personal_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('personal_tasks', 'my_day_for')) $table->dropColumn('my_day_for');
            if (Schema::hasColumn('personal_tasks', 'recurrence_type')) $table->dropColumn('recurrence_type');
            if (Schema::hasColumn('personal_tasks', 'recurrence_weekdays')) $table->dropColumn('recurrence_weekdays');
        });
        Schema::dropIfExists('personal_task_subtasks');
    }
};

