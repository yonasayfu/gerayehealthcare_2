<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shared_invoices', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable()->unique()->after('notes');
            $table->timestamp('share_expires_at')->nullable()->after('share_token');
            $table->index('share_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('shared_invoices', function (Blueprint $table) {
            $table->dropIndex(['share_expires_at']);
            $table->dropUnique(['share_token']);
            $table->dropColumn(['share_token', 'share_expires_at']);
        });
    }
};

