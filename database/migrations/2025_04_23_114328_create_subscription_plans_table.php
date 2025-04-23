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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->index();
            $table->unsignedBigInteger('price')->index();
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedTinyInteger('is_lifetime')->default(0)->comment('0 => false, 1 => true');
            $table->enum('type', ['personal', 'company'])->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('0 => false, 1 => true');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
