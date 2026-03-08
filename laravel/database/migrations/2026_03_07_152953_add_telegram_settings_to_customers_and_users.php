<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable(); // ID чата руководителя
            $table->boolean('notify_owner_tg')->default(false); // Флаг ТГ для руководителя
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_manager_tg')->default(false); // Флаг ТГ для менеджера
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['telegram_chat_id', 'notify_owner_tg']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notify_manager_tg');
        });
    }
};

