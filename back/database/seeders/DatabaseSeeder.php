<?php

namespace Database\Seeders;

use App\Models\Avatar;
use App\Models\City;
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

        $city = City::factory()->create(['name' => 'Zagreb']);

        foreach (self::ADMIN_USERS as $user => $email) {
            User::factory()->create([
                'name' => $user,
                'email' => $email,
                'avatar_id' => $avatars[0]->id,
                'city_id' => $city->id
            ]);
        }

        $this->call(
            CategorySeeder::class,
        );
    }
}
