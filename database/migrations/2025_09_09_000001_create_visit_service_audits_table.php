<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visit_service_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_service_id')->constrained('visit_services')->onDelete('cascade');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('from_check_in_time')->nullable();
            $table->timestamp('to_check_in_time')->nullable();
            $table->timestamp('from_check_out_time')->nullable();
            $table->timestamp('to_check_out_time')->nullable();
            $table->string('reason', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_service_audits');
    }
};

