<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MillingNameEnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasColumn('millings', 'name_en')) {
            $this->command->warn('Column millings.name_en not found. Run migration first.');

            return;
        }
        $map = [
            // ручки
             'L ручка' => 'Lhandle',
            'u ручка' => 'uhandle',
            // Halina
             'Halina32' => 'Halina32',
            'Halina4' => 'Halina4',
            // Sparta
             'Спарта' => 'Sparta',
            // Bravo
             'Брава' => 'Bravo',
            'Брава+Волна1' => 'Bravo+Wave1',
            'Брава+Волна2' => 'Bravo+Wave2',
            // Vienna
            // (только подтверждённые в facade_types)
             'ViennaPL' => 'ViennaPL',
            'ViennaPL"X"' => 'ViennaPL"X"',
            'ViennaPL"H"' => 'ViennaPL"H"',
            'Vienna' => 'Vienna',
            // Линии
            'Линии' => 'Line',
            // Волны (только подтверждённые в facade_types)
             'Волна' => 'Wave',
            'Волна2' => 'Wave2',
         ];

        foreach ($map as $ru => $en) {
            DB::table('millings')->where('name', $ru)->update(['name_en' => $en]);
        }
        DB::table('millings')->whereNull('name_en')->update([
            'name_en' => DB::raw('name')
        ]);

        $updated = DB::table('millings')->whereNotNull('name_en')->count();
        $nulls = DB::table('millings')->whereNull('name_en')->count();

        $this->command->info("millings.name_en filled: {$updated}, nulls remaining: {$nulls}");
    }


}
