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
            $table->decimal('price_retail', 10, 2)->nullable()->after('code'); // розничная цена
            $table->decimal('price_dealer', 10, 2)->nullable()->after('price_retail'); // цена для дилеров
            $table->decimal('price_private', 10, 2)->nullable()->after('price_dealer'); // цена для физ. лиц
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('millings', function (Blueprint $table) {
            $table->dropColumn(['price_retail', 'price_dealer', 'price_private']);
        });
    }
};
