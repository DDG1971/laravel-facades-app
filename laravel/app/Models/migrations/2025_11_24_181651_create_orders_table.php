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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // связи (только поля, без FK)
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('status_id');

            $table->date('date_received')->nullable();     // дата прихода заказа
            $table->timestamp('date_status')->nullable();  // дата изменения статуса

            // основные поля
            $table->string('order_number')->default('б/н');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->decimal('square_meters', 10, 2)->nullable();

            // финансовая логика
            $table->enum('payment_status', ['unpaid','partial','paid'])->default('unpaid');
            $table->decimal('prepayment', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);

            // справочники (только поля)
            $table->unsignedBigInteger('coating_type_id')->nullable();
            $table->unsignedBigInteger('color_catalog_id')->nullable();
            $table->unsignedBigInteger('color_code_id')->nullable();
            $table->unsignedBigInteger('milling_id')->nullable();

            // доп. поля
            $table->tinyInteger('paint_shop_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

