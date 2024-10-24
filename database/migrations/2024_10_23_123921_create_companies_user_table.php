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
        Schema::create('companies_user', function (Blueprint $table) {
            $table->foreignId('company_id')->comment('The Company')->constrained('companies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->comment('The Simple user')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_user');
    }
};
