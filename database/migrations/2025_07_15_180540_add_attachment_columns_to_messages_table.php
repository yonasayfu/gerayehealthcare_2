<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add columns for attachment details
            $table->string('attachment_path')->nullable()->after('message');
            $table->string('attachment_filename')->nullable()->after('attachment_path');
            $table->string('attachment_mime_type')->nullable()->after('attachment_filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop columns if rolling back
            $table->dropColumn(['attachment_path', 'attachment_filename', 'attachment_mime_type']);
        });
    }
};
