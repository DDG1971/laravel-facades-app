<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drillings = [
            ['name_en' => 'hinges',    'name_ru' => 'Петли'],
            ['name_en' => 'grooves',   'name_ru' => 'Паз под ДВП'],
            ['name_en' => 'rectifier', 'name_ru' => 'С выпрямителем'],
        ];

        foreach ($drillings as $drilling) {
            DB::table('drillings')->updateOrInsert(
                ['name_en' => $drilling['name_en']], // уникальный ключ
                ['name_ru' => $drilling['name_ru']]
            );
        }
    }
}

