<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_services', function (Blueprint $table) {
            $table->string('service_type')->default('general')->after('visit_notes');
            $table->string('priority')->default('normal')->after('service_type');
            $table->string('visit_location')->nullable()->after('priority');
            $table->string('patient_location')->nullable()->after('visit_location');
            $table->string('gps_coordinates')->nullable()->after('patient_location');

            $table->text('patient_condition')->nullable()->after('gps_coordinates');
            $table->text('treatment_provided')->nullable()->after('patient_condition');
            $table->text('medications_administered')->nullable()->after('treatment_provided');

            $table->boolean('follow_up_required')->default(false)->after('medications_administered');
            $table->date('follow_up_date')->nullable()->after('follow_up_required');
            $table->text('follow_up_notes')->nullable()->after('follow_up_date');

            $table->string('payment_status')->nullable()->after('follow_up_notes');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->foreignId('insurance_claim_id')->nullable()->after('payment_method')->constrained('insurance_claims')->nullOnDelete();

            $table->decimal('rating', 3, 2)->nullable()->after('insurance_claim_id');
            $table->text('feedback')->nullable()->after('rating');

            $table->text('cancellation_reason')->nullable()->after('feedback');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();

            $table->string('check_in_location')->nullable()->after('check_in_longitude');
            $table->string('check_out_location')->nullable()->after('check_out_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('visit_services', function (Blueprint $table) {
            $table->dropForeign(['insurance_claim_id']);
            $table->dropForeign(['cancelled_by']);

            $table->dropColumn([
                'service_type',
                'priority',
                'visit_location',
                'patient_location',
                'gps_coordinates',
                'patient_condition',
                'treatment_provided',
                'medications_administered',
                'follow_up_required',
                'follow_up_date',
                'follow_up_notes',
                'payment_status',
                'payment_method',
                'insurance_claim_id',
                'rating',
                'feedback',
                'cancellation_reason',
                'cancelled_at',
                'cancelled_by',
                'check_in_location',
                'check_out_location',
            ]);
        });
    }
};
