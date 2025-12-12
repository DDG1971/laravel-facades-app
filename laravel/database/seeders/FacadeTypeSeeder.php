<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class FacadeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Загружаем CSV-файл
        $csv = Reader::createFromPath(database_path('seeders/data/facade_types.csv'), 'r');
        $csv->setHeaderOffset(0); // первая строка — заголовки

        foreach ($csv as $record) {
            DB::table('facade_types')->updateOrInsert(
                ['name_en' => $record['name_en']], // уникальный ключ
                ['name_ru' => $record['name_ru']]
            );
        }
    }
}
