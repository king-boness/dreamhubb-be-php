<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Continent;

class ContinentSeeder extends Seeder
{
    public function run(): void
    {
        $continents = [
            ['name' => 'Europe'],
            ['name' => 'Asia'],
            ['name' => 'Africa'],
            ['name' => 'North America'],
            ['name' => 'South America'],
            ['name' => 'Australia'],
            ['name' => 'Antarctica'],
        ];

        Continent::insert($continents);
    }
}
