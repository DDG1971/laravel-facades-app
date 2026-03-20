<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 1. Старым заказам, где NULL, прописываем ID 1 (Г)
            // Делаем это через сырой запрос, чтобы не было ошибок
            DB::table('orders')->whereNull('paint_shop_id')->update(['paint_shop_id' => 1]);

            // 2. Устанавливаем дефолтное значение и foreign key
            $table->unsignedBigInteger('paint_shop_id')->default(1)->change();
            $table->foreign('paint_shop_id')->references('id')->on('paint_shops');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['paint_shop_id']);
            $table->unsignedBigInteger('paint_shop_id')->nullable()->default(null)->change();
        });
    }
};
