<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CountrySubdivision>
 */
class CountrySubdivisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->unique(false, 6)->text(6),
            'name' => $this->faker->text('6'),
            'country_id' => Country::factory()->create(),
        ];
    }
}
