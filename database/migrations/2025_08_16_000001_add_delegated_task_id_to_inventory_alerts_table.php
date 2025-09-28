<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_alerts', function (Blueprint $table) {
            $table->foreignId('delegated_task_id')
                ->nullable()
                ->constrained('task_delegations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_alerts', function (Blueprint $table) {
            $table->dropForeign(['delegated_task_id']);
            $table->dropColumn('delegated_task_id');
        });
    }
};
