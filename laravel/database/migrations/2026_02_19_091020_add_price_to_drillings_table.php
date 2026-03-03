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
        Schema::table('drillings', function (Blueprint $table) {
            //  цены (10 цифр всего, 2 после запятой)
            $table->decimal('price', 10, 2)->default(0.00)->after('name_ru');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drillings', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
