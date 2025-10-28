<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Country;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $slovakia = Country::where('name', 'Slovakia')->first();
        $czech = Country::where('name', 'Czech Republic')->first();

        $cities = [
            ['name' => 'Bratislava', 'country_id' => $slovakia->id],
            ['name' => 'Košice', 'country_id' => $slovakia->id],
            ['name' => 'Praha', 'country_id' => $czech->id],
            ['name' => 'Brno', 'country_id' => $czech->id],
        ];

        City::insert($cities);
    }
}
