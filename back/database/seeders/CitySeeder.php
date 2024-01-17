<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    const CITY_NAMES = [
        'Zagreb'
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::CITY_NAMES as $cityName) {
            City::factory()->create(['name' => $cityName]);
        }
    }
}
