<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff_payouts', function (Blueprint $table) {
            $table->unsignedBigInteger('requested_by')->nullable()->after('notes');
            $table->unsignedBigInteger('processed_by')->nullable()->after('requested_by');
            $table->text('processed_notes')->nullable()->after('processed_by');
            $table->unsignedBigInteger('reverted_by')->nullable()->after('processed_notes');
            $table->text('reverted_reason')->nullable()->after('reverted_by');
            $table->timestamp('reverted_at')->nullable()->after('reverted_reason');

            $table->index('requested_by');
            $table->index('processed_by');
            $table->index('reverted_by');
        });
    }

    public function down(): void
    {
        Schema::table('staff_payouts', function (Blueprint $table) {
            $table->dropIndex(['requested_by']);
            $table->dropIndex(['processed_by']);
            $table->dropIndex(['reverted_by']);

            $table->dropColumn(['requested_by', 'processed_by', 'processed_notes', 'reverted_by', 'reverted_reason', 'reverted_at']);
        });
    }
};

