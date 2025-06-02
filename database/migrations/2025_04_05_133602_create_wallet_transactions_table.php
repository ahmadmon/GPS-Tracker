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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->nullableMorphs('source'); // The actor who initiated the transaction (e.g., user, admin,  company)

            $table->enum('type', ['credit', 'debit']);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');

            $table->unsignedBigInteger('amount');
            $table->string('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
