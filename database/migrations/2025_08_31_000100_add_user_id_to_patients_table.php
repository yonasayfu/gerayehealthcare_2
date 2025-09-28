<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->index('user_id');
        });

        // Best-effort backfill: match patients to users by email
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('UPDATE patients SET user_id = (SELECT id FROM users WHERE LOWER(users.email) = LOWER(patients.email)) WHERE user_id IS NULL AND email IS NOT NULL');
        } else {
            DB::statement('UPDATE patients p SET user_id = u.id FROM users u WHERE p.user_id IS NULL AND p.email IS NOT NULL AND LOWER(p.email) = LOWER(u.email)');
        }
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
