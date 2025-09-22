<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->index()->after('attachment_mime_type');
        });

        Schema::table('group_messages', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->index()->after('attachment_mime_type');
        });
    }

    public function down(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            $table->dropColumn('is_pinned');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('is_pinned');
        });
    }
};
