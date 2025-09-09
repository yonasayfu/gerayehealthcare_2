<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_delegations', function (Blueprint $table) {
            if (!Schema::hasColumn('task_delegations', 'acceptance_status')) {
                $table->string('acceptance_status')->default('Pending'); // Pending|Accepted|Rejected
            }
            if (!Schema::hasColumn('task_delegations', 'response_notes')) {
                $table->text('response_notes')->nullable();
            }
            if (!Schema::hasColumn('task_delegations', 'responded_at')) {
                $table->dateTime('responded_at')->nullable();
            }
            if (!Schema::hasColumn('task_delegations', 'responded_by')) {
                $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('task_delegations', function (Blueprint $table) {
            if (Schema::hasColumn('task_delegations', 'responded_by')) {
                $table->dropConstrainedForeignId('responded_by');
            }
            foreach (['acceptance_status','response_notes','responded_at'] as $col) {
                if (Schema::hasColumn('task_delegations', $col)) $table->dropColumn($col);
            }
        });
    }
};

