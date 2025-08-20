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
        Schema::table('event_staff_assignments', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('role');
            $table->unique(['event_id', 'staff_id'], 'event_staff_assignments_event_staff_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_staff_assignments', function (Blueprint $table) {
            $table->dropUnique('event_staff_assignments_event_staff_unique');
            $table->dropColumn('notes');
        });
    }
};
