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
        Schema::create('subscription_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->tinyText('reason');
            $table->string('iban')->nullable();
            $table->unsignedBigInteger('refund_amount')->nullable();
            $table->enum('status', ['pending', 'refunded', 'rejected'])->default('pending');
            $table->tinyText('rejected_reason')->nullable();
            $table->timestamp('canceled_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_cancellations');
    }
};
