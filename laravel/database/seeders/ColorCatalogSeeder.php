<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ColorCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $csv = Reader::createFromPath(database_path('seeders/data/color_catalogs.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            DB::table('color_catalogs')->updateOrInsert(
                ['name_en' => $record['name_en']], // уникальный ключ
                [] // дополнительных полей нет, оставляем пустой массив
            );
        }
    }
}
