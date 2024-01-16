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

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $avatars = [];
        $i = 1;

        while ($i < 31) {
            $avatars[] = Avatar::factory()->create([
                'path' => "avatars/default$i.png",
                'default' => true,
            ]);

            $i++;
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
