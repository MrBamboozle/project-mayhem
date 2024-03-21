<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = City::factory()->create();

        return [
            'title' => $this->faker->title,
            'tag_line' => $this->faker->text,
            'description' => $this->faker->text,
            'start_time' => Carbon::now()->addDays(mt_rand(1, 3)),
            'end_time' => Carbon::now()->addDays(mt_rand(4, 10)),
            'location' => implode(',', $this->faker->localCoordinates),
            'address' => json_encode(['city' => $city->name, 'countrySubdivisionId' => $city->country_subdivision_id]),
            'user_id' => User::factory()->create(),
            'city_id' => $city,
        ];
    }
}
