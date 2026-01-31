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
        Schema::create('thicknesses', function (Blueprint $table) {
            $table->id();
            $table->integer('value');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('label')->nullable(); // "16 мм"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thicknesses');
    }
};
