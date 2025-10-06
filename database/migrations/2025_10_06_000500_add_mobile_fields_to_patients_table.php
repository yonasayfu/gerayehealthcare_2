<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('postal_code', 20)->nullable()->after('country');

            $table->string('emergency_contact_name')->nullable()->after('emergency_contact');
            $table->string('emergency_contact_phone', 50)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship', 100)->nullable()->after('emergency_contact_phone');

            $table->text('medical_history')->nullable()->after('emergency_contact_relationship');
            $table->text('allergies')->nullable()->after('medical_history');
            $table->text('current_medications')->nullable()->after('allergies');

            $table->string('insurance_provider')->nullable()->after('current_medications');
            $table->string('insurance_policy_number')->nullable()->after('insurance_provider');
            $table->string('preferred_language', 50)->nullable()->after('insurance_policy_number');
            $table->string('blood_type', 5)->nullable()->after('preferred_language');

            $table->decimal('height', 5, 2)->nullable()->after('blood_type');
            $table->decimal('weight', 5, 2)->nullable()->after('height');

            $table->string('status', 50)->default('active')->after('weight');
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'state',
                'country',
                'postal_code',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
                'medical_history',
                'allergies',
                'current_medications',
                'insurance_provider',
                'insurance_policy_number',
                'preferred_language',
                'blood_type',
                'height',
                'weight',
                'status',
                'notes',
            ]);
        });
    }
};
