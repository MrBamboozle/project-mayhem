<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\CountrySubdivision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'country_subdivision_id' => CountrySubdivision::factory()->create(),
        ];
    }
}
