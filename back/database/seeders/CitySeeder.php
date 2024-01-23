<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\CountrySubdivision;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (config('countries') as $country) {
            $countryObj = Country::factory()->createOne([
                'id' => $country['id'],
                'name' => $country['name'],
            ]);

            $countrySubdivisions = $country['country_subdivisions'] ?? [];

            foreach ($countrySubdivisions as $countrySubdivision) {
                $countrySubdivisionObj = CountrySubdivision::factory()->createOne([
                    'id' => $countrySubdivision['id'],
                    'name' => $countrySubdivision['name'],
                    'country_id' => $countryObj->id,
                ]);

                $cities = $countrySubdivision['cities'] ?? [];

                foreach ($cities as $city) {
                    City::factory()->createOne([
                        'name' => $city,
                        'country_subdivision_id' => $countrySubdivisionObj->id,
                    ]);
                }
            }
        }
    }
}
