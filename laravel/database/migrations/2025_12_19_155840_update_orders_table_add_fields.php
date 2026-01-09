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
            // переименоваю order_number → queue_number
            $table->renameColumn('order_number', 'queue_number');

            // добавляю новые поля
            $table->string('client_order_number')->nullable()->after('queue_number');
            $table->enum('material', ['MDF','Shpon'])->nullable()->after('client_order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->renameColumn('queue_number', 'order_number');
            $table->dropColumn(['client_order_number', 'material']);
        });
    }
};
