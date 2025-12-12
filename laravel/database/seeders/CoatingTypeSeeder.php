<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class CoatingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv = Reader::createFromPath(
            database_path('seeders/data/coating_types.csv'),
            'r'
        );
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            DB::table('coating_types')->updateOrInsert(
                ['name' => $record['name']],   // уникальный системный ключ
                ['label' => $record['label']] // обновляем человекочитаемое название
            );
        }
    }
}
