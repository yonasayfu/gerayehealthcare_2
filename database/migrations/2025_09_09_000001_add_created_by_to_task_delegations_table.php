<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_delegations', function (Blueprint $table) {
            if (!Schema::hasColumn('task_delegations', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->index('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('task_delegations', function (Blueprint $table) {
            if (Schema::hasColumn('task_delegations', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};

