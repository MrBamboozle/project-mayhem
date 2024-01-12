<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Avatar;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const ADMIN_USERS = [
        'Filip Petrina' => 'filippetrina92@gmail.com',
        'Domagoj Bišćan' => 'd.biscan.stu@gmail.com'
    ];

    const AVATARS = [
        'default1.png',
        'default2.png',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $avatars = [];

        foreach (self::AVATARS as $avatar) {
            $avatars[] = Avatar::factory()->create([
                'path' => "avatars/$avatar"
            ]);
        }

        foreach (self::ADMIN_USERS as $user => $email) {
            User::factory()->create([
                'name' => $user,
                'email' => $email,
                'avatar_id' => $avatars[0]->id
            ]);
        }
    }
}
