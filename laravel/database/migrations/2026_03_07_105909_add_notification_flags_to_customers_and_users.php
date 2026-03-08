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
        // Флаг для руководителя компании
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('notify_owner')->default(true);
        });

        // Флаг для конкретного менеджера
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_manager')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('notify_owner');
        });


        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notify_manager');
        });
    }
};
