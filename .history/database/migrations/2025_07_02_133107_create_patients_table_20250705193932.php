<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('emergency_contact')->nullable();
            $table->text('geolocation')->nullable(); // Use PostGIS if needed later
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('patients');
    }
};
