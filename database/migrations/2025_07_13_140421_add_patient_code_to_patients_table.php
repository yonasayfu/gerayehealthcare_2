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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('patient_code')->nullable()->after('id');
        });

        // Update existing records with unique patient codes
        $patients = \App\Models\Patient::whereNull('patient_code')->get();
        foreach ($patients as $patient) {
            do {
                $code = 'P'.str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            } while (\App\Models\Patient::where('patient_code', $code)->exists());

            $patient->update(['patient_code' => $code]);
        }

        // Make the column not nullable and unique
        Schema::table('patients', function (Blueprint $table) {
            $table->string('patient_code')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('patient_code');
        });
    }
};
