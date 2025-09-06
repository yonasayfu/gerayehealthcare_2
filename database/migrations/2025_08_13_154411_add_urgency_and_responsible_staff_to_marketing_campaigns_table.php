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
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->string('urgency')->nullable()->after('status'); // e.g., 'High', 'Medium', 'Low'
            $table->foreignId('responsible_staff_id')->nullable()->constrained('staff')->onDelete('set null')->after('urgency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->dropForeign(['responsible_staff_id']);
            $table->dropColumn(['urgency', 'responsible_staff_id']);
        });
    }
};
