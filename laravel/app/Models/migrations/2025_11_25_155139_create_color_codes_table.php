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
        Schema::create('color_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('color_catalog_id'); // FK → color_catalogs
            $table->string('code'); // сам код (например: 9010)
            $table->timestamps();

            $table->foreign('color_catalog_id')
                  ->references('id')
                  ->on('color_catalogs')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_codes');
    }
};
