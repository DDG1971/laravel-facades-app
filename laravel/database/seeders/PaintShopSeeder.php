<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaintShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\PaintShop::updateOrCreate(['id' => 1], ['name' => 'Г', 'full_name' => 'Главный цех']);
        \App\Models\PaintShop::updateOrCreate(['id' => 2], ['name' => 'К', 'full_name' => 'Цех на Кабяка']);
    }
}
