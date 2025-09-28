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
        Schema::table('marketing_tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_staff_id']);
            $table->foreign('assigned_to_staff_id')
                ->references('id')
                ->on('staff')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_staff_id']);
            $table->foreign('assigned_to_staff_id')
                ->references('id')
                ->on('staff')
                ->onDelete('cascade');
        });
    }
};
