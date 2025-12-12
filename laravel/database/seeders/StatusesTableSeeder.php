<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'new',         'label' => 'Новый'],
            ['name' => 'received',    'label' => 'В очереди'],
            ['name' => 'in_progress', 'label' => 'В работе'],
            ['name' => 'paint_shop',  'label' => 'В покраске'],
            ['name' => 'ready',       'label' => 'Готово'],
            ['name' => 'shipped',     'label' => 'Отгружено'],
            ['name' => 'completed',   'label' => 'Завершён'],
            ['name' => 'cancelled',   'label' => 'Отменён'],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['name' => $status['name']],   // уникальный системный ключ
                ['label' => $status['label']]  // обновляем человекочитаемое название
            );
        }
    }
}
