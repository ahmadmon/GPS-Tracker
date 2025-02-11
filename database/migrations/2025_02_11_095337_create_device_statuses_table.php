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
        Schema::create('device_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('ac_status')->nullable()->comment("True => Air Condition ON, False => AC OFF");
            $table->boolean('ignition')->nullable()->comment("True => ACC ON, False => ACC OFF");
            $table->boolean('charging')->nullable();
            $table->enum('alarm_type', ['shock', 'power_cut', 'low_battery', 'sos', 'normal'])->nullable();
            $table->boolean('gps_tracking')->nullable()->comment("True => Gps Tracking is ON, False => Gps Tracking is OFF");
            $table->boolean('relay_state')->nullable()->comment("True => Relay ON (engine is off), False => Relay OFF (engine is on)");
            $table->unsignedTinyInteger('voltage_level')->nullable()->comment("0 => no power (shutdown), 1 => extremely low battery, 2 => very low battery, 3 => low battery (can be used normally), 4 => medium, 5 => high, 6 => very high, 7 => unknown");
            $table->unsignedTinyInteger('signal_level')->nullable()->comment("0 => no signal, 1 => extremely weak signal, 2 => very weak signal, 3 => good signal, 4 => strong signal, 5 => unknown");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_statuses');
    }
};
