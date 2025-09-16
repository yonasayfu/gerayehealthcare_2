<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_delegations', function (Blueprint $table) {
            $table->unsignedBigInteger('partner_id')->nullable()->after('assigned_to');
            $table->foreign('partner_id')->references('id')->on('partners')->nullOnDelete();
            $table->index('partner_id');
            $table->index('task_category');
        });
    }

    public function down(): void
    {
        Schema::table('task_delegations', function (Blueprint $table) {
            $table->dropIndex(['partner_id']);
            $table->dropIndex(['task_category']);
            $table->dropForeign(['partner_id']);
            $table->dropColumn('partner_id');
        });
    }
};

