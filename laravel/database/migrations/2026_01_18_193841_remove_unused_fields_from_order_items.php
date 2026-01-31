<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // сначала удаляем foreign keys
             $table->dropForeign(['milling_id']);
             $table->dropForeign(['coating_type_id']);
             $table->dropForeign(['color_catalog_id']);
             $table->dropForeign(['color_code_id']);
             // потом удаляем сами поля
             $table->dropColumn([
                 'milling_id',
                 'coating_type_id',
                 'color_catalog_id',
                 'color_code_id',
                 ]);
        });
    }
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('milling_id')->nullable();
            $table->foreign('milling_id')->references('id')->on('millings')->onDelete('restrict');

            $table->unsignedBigInteger('coating_type_id')->nullable();
            $table->foreign('coating_type_id')->references('id')->on('coating_types')->onDelete('restrict');

            $table->unsignedBigInteger('color_catalog_id')->nullable();
            $table->foreign('color_catalog_id')->references('id')->on('color_catalogs')->onDelete('restrict');

            $table->unsignedBigInteger('color_code_id')->nullable();
            $table->foreign('color_code_id')->references('id')->on('color_codes')->onDelete('restrict');
        });
    }
};


