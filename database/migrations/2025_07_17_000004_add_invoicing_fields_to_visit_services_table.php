<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_services', function (Blueprint $table) {
            $table->string('service_description')->nullable()->after('visit_notes');
            $table->boolean('is_invoiced')->default(false)->after('is_paid_to_staff');
        });
    }

    public function down(): void
    {
        Schema::table('visit_services', function (Blueprint $table) {
            $table->dropColumn(['service_description', 'is_invoiced']);
        });
    }
};
