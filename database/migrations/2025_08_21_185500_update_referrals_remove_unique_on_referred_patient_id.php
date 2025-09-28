<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            // Drop the UNIQUE constraint on referred_patient_id to allow multiple referrals per patient
            $table->dropUnique('referrals_referred_patient_id_unique');
            // Add a normal index for performance instead
            $table->index('referred_patient_id');
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            // Drop the normal index
            $table->dropIndex('referrals_referred_patient_id_index');
            // Restore the UNIQUE constraint
            $table->unique('referred_patient_id');
        });
    }
};
