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
        Schema::table('facade_types', function (Blueprint $table) {
            $table->enum('pricing_mode', ['inherit', 'set_base', 'percent_add', 'none'])
                ->default('inherit')
                ->after('name_ru');

            // Значение правила:
            // - set_base → новая базовая цена ($)
            // - percent_add → процент (%)
            // - none / inherit → 0
            $table->decimal('pricing_value', 10, 2)
                ->default(0)
                ->after('pricing_mode');

            // Единица измерения:
            // inherit → брать из Milling
            // piece → поштучно
            // m2 → за м²
            // lm → за погонный метр
            $table->enum('unit_mode', ['inherit', 'piece', 'm2', 'lm'])
                ->default('inherit')
                ->after('pricing_value');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facade_types', function (Blueprint $table) {
            $table->dropColumn(['pricing_mode', 'pricing_value', 'unit_mode']);
        });
    }
};
