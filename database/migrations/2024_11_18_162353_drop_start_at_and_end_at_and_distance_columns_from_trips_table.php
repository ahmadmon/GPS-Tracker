<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
            $table->dropColumn('distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->timestamp('start_at')->nullable()->after('device_stats');
            $table->timestamp('end_at')->nullable()->after('device_stats');
            $table->bigInteger('distance')->default(0)->after('long');
        });
    }
};
