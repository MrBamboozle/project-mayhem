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
        'Domagoj Bišćan' => 'd.biscan.stu@gmail.com',
        'Dodo' => 'dodo@bobo.com',
        'Tester 1' => 'test1@gmail.com',
        'Tester 2' => 'test2@gmail.com',
        'Tester 3' => 'test3@gmail.com',
        'Tester 4' => 'test4@gmail.com',
        'Tester 5' => 'test5@gmail.com',
        'Tester 6' => 'test6@gmail.com',
        'Tester 7' => 'test7@gmail.com',
        'Tester 8' => 'test8@gmail.com',
        'Tester 9' => 'test9@gmail.com',
        'Tester 10' => 'test10@gmail.com',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            CitySeeder::class,
        ]);

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
                'avatar_id' => $avatars[0]->id,
                'city_id' => City::first()->id
            ]);
        }
    }
}
