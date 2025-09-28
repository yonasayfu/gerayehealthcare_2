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
        Schema::table('referral_documents', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('document_path');
            $table->string('mime_type', 150)->nullable()->after('original_name');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            $table->string('checksum', 64)->nullable()->after('file_size'); // sha256
            $table->index('checksum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_documents', function (Blueprint $table) {
            $table->dropIndex(['checksum']);
            $table->dropColumn(['original_name', 'mime_type', 'file_size', 'checksum']);
        });
    }
};
