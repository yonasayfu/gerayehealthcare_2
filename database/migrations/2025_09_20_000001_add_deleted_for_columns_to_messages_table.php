<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'deleted_for_sender_at')) {
                $table->timestamp('deleted_for_sender_at')->nullable()->after('edited_at');
            }
            if (!Schema::hasColumn('messages', 'deleted_for_receiver_at')) {
                $table->timestamp('deleted_for_receiver_at')->nullable()->after('deleted_for_sender_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'deleted_for_sender_at')) {
                $table->dropColumn('deleted_for_sender_at');
            }
            if (Schema::hasColumn('messages', 'deleted_for_receiver_at')) {
                $table->dropColumn('deleted_for_receiver_at');
            }
        });
    }
};

