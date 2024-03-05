<?php

namespace Database\Factories;

use App\Models\PasswordResetToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordResetTokenFactory extends Factory
{
    protected $model = PasswordResetToken::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->email,
            'token' => $this->faker->uuid,
            'created_at' => now(),
        ];
    }
}
