<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
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
            'time' => $this->faker->dateTime,
            'location' => $this->faker->localCoordinates,
            'user_id' => null,
            'city_id' => null,
        ];
    }
}
