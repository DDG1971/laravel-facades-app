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
        Schema::table('order_items', function (Blueprint $table) {
            // Сначала убираем старый foreign key
            $table->dropForeign(['facade_type_id']);

            // Меняем колонку на nullable
            $table->unsignedBigInteger('facade_type_id')->nullable()->change();

            // Заново навешиваем внешний ключ
            $table->foreign('facade_type_id')
                ->references('id')->on('facade_types')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['facade_type_id']);
            $table->unsignedBigInteger('facade_type_id')->change();
            $table->foreign('facade_type_id')
                ->references('id')->on('facade_types')
                ->onDelete('restrict');
        });
    }
};
