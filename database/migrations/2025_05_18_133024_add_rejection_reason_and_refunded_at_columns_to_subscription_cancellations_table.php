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
        Schema::table('subscription_cancellations', function (Blueprint $table) {
            $table->renameColumn('rejected_reason', 'rejection_reason');
            $table->timestamp('refunded_at')->nullable()->after('canceled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_cancellations', function (Blueprint $table) {
            $table->dropColumn('refunded_at');
            $table->renameColumn('rejection_reason', 'rejected_reason');
        });
    }
};
