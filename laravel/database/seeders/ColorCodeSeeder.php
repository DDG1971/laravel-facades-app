<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ColorCodeSeeder extends Seeder
{
    public function run(): void
    {
        $csv = Reader::createFromPath(database_path('seeders/data/color_codes.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            $code = trim($record['code'] ?? '');
            $catalogName = trim($record['color_catalog'] ?? '');

            if ($code === '' || $catalogName === '') {
                continue;
            }

            $catalogId = DB::table('color_catalogs')
                ->where('name_en', $catalogName)
                ->value('id');

            if ($catalogId) {
                DB::table('color_codes')->updateOrInsert(
                    ['code' => $code],
                    [
                        'color_catalog_id' => $catalogId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
