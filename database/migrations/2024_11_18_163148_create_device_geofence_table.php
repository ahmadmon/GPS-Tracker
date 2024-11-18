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
        Schema::create('device_geofence', function (Blueprint $table) {
            $table->foreignId('device_id')->constrained('devices')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('geofence_id')->constrained('geofences')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('is_inside');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('long', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_geofence');
    }
};
