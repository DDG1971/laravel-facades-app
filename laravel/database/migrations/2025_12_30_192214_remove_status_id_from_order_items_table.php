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
            // сначала удалю внешний ключ
             $table->dropForeign(['status_id']);
             // затем сам столбец
             $table->dropColumn('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('status_id')
                ->constrained('statuses')
                ->onDelete('restrict');
        });
    }
};
