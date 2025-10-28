<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Všeobecné',
                'icon' => 'fa-solid fa-globe',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Osobný rozvoj',
                'icon' => 'fa-solid fa-brain',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pomoc ostatným',
                'icon' => 'fa-solid fa-hands-helping',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tvorba & projekty',
                'icon' => 'fa-solid fa-lightbulb',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
