<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Системные пользователи (идемпотентно)
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin123'),
            ]
        );

        // Справочники (все сидеры переписаны на updateOrInsert)
        $this->call([
            StatusesTableSeeder::class,
            DrillingSeeder::class,
            FacadeTypeSeeder::class,
            MillingSeeder::class,
            ColorCatalogSeeder::class,
            ColorCodeSeeder::class,
            CoatingTypeSeeder::class,
        ]);
    }
}
