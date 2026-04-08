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
        Schema::table('millings', function (Blueprint $table) {
            $table->decimal('price_coloring', 10, 2)->nullable()->after('price_private'); // цена для сырого мдф
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('millings', function (Blueprint $table) {
            $table->dropColumn(['price_coloring']);
        });
    }
};
