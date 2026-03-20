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
        Schema::create('paint_shops', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Тут будет "Г" или "К"
            $table->string('full_name')->nullable(); // Например, "Главный" или "Красная ул."
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paint_shops');
    }
};
