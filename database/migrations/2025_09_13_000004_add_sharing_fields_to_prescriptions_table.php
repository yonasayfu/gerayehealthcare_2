<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('share_token', 64)->nullable()->unique()->after('created_by_staff_id');
            $table->timestamp('share_expires_at')->nullable()->after('share_token');
            $table->unsignedInteger('share_views')->default(0)->after('share_expires_at');
            $table->timestamp('last_viewed_at')->nullable()->after('share_views');
            $table->string('share_pin', 20)->nullable()->after('last_viewed_at');
            $table->index('share_expires_at');
            $table->index('share_views');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropIndex(['share_expires_at']);
            $table->dropIndex(['share_views']);
            $table->dropUnique(['share_token']);
            $table->dropColumn(['share_token','share_expires_at','share_views','last_viewed_at','share_pin']);
        });
    }
};

