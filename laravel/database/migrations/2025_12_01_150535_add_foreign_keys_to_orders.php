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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('status_id')->references('id')->on('statuses')->restrictOnDelete();

            $table->foreign('coating_type_id')->references('id')->on('coating_types')->restrictOnDelete();
            $table->foreign('color_catalog_id')->references('id')->on('color_catalogs')->restrictOnDelete();
            $table->foreign('color_code_id')->references('id')->on('color_codes')->restrictOnDelete();
            $table->foreign('milling_id')->references('id')->on('millings')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['coating_type_id']);
            $table->dropForeign(['color_catalog_id']);
            $table->dropForeign(['color_code_id']);
            $table->dropForeign(['milling_id']);
        });
    }
};
