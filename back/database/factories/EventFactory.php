<?php

namespace Database\Factories;

use App\Models\Event;
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
        return [
            'title' => $this->faker->title,
            'tag_line' => $this->faker->text,
            'description' => $this->faker->text,
            'start_time' => $this->faker->dateTime,
            'end_time' => $this->faker->dateTime,
            'location' => $this->faker->localCoordinates,
            'user_id' => null,
            'city_id' => null,
        ];
    }
}
