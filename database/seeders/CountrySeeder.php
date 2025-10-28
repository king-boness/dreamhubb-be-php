<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Continent;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $europe = Continent::where('name', 'Europe')->first();

        $countries = [
            ['name' => 'Slovakia', 'continent_id' => $europe->id],
            ['name' => 'Czech Republic', 'continent_id' => $europe->id],
            ['name' => 'Poland', 'continent_id' => $europe->id],
            ['name' => 'Germany', 'continent_id' => $europe->id],
        ];

        Country::insert($countries);
    }
}
