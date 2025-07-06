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
    Schema::create('staff_availabilities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('staff_id')->constrained()->onDelete('cascade');
        $table->timestamp('start_time');
        $table->timestamp('end_time');
        $table->string('status'); // No default - status must be explicitly provided.
        $table->timestamps();
    });
}
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_availabilities');
    }
};
