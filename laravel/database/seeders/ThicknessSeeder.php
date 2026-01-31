<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThicknessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thicknesses')->insert([
            ['value' => 6, 'price' => 0, 'label' => '6 мм'],
            ['value' => 10, 'price' => 0, 'label' => '10 мм'],
            ['value' => 12, 'price' => 0, 'label' => '12 мм'],
            ['value' => 16, 'price' => 0, 'label' => '16 мм'],
            ['value' => 18, 'price' => 0, 'label' => '18 мм'],
            ['value' => 19, 'price' => 0, 'label' => '19 мм'],
            ['value' => 22, 'price' => 0, 'label' => '22 мм'],
            ['value' => 25, 'price' => 0, 'label' => '25 мм'],
            ['value' => 32, 'price' => 0, 'label' => '32 мм'],
            ['value' => 38, 'price' => 0, 'label' => '38 мм'],
            ['value' => 44, 'price' => 0, 'label' => '44 мм'],
        ]);
    }
}
