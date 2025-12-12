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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            //  Связь с заказом
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            //  Справочники
            $table->foreignId('facade_type_id')->constrained()->onDelete('restrict');
            $table->foreignId('milling_id')->nullable()->constrained()->onDelete('set null');

            //  Цветовые справочники (как в orders)
            $table->foreignId('coating_type_id')->nullable()->constrained('coating_types')->onDelete('restrict');
            $table->foreignId('color_catalog_id')->nullable()->constrained('color_catalogs')->onDelete('restrict');
            $table->foreignId('color_code_id')->nullable()->constrained('color_codes')->onDelete('restrict');

            //  Основные характеристики
            $table->string('material');
            $table->enum('thickness', ['6','10','12','14','16','18','19','22','25','32','38','44']);
            $table->integer('height');
            $table->integer('width');
            $table->decimal('square_meters', 10, 2)->nullable();
            $table->integer('quantity');
            $table->boolean('double_sided_coating')->default(false);

            //  Технология сверления
            $table->foreignId('drilling_id')->nullable()->constrained('drillings')->onDelete('set null');

            //  Дополнительно для уточнения
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();

            //  Цены
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid','partial','paid'])->default('unpaid');
            $table->decimal('prepayment', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);

            //  Workflow позиции
            $table->foreignId('status_id')->constrained('statuses')->onDelete('restrict');
            $table->timestamp('date_status')->nullable();   // когда изменился статус позиции
            $table->date('date_created')->nullable();       // когда позиция была оформлена

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
