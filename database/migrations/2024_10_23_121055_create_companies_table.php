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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->text('logo')->nullable();
            $table->text('address');
            $table->string('contact_number');
            $table->foreignId('user_id')->comment('The Manager')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('status')->default(1)->comment('0 => disabled, 1 => enabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
