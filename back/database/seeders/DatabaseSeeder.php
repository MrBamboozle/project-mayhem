<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Avatar;
use App\Models\City;
use App\Models\Event;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RouteConfiguration;
use App\Models\User;
use Database\Factories\RoleFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const GOD_MODE_USERS = [
        'Filip Petrina' => 'filippetrina92@gmail.com',
        'Domagoj Bišćan' => 'd.biscan.stu@gmail.com',
    ];

    const ADMINS = [
        'Mycho' => 'Mycho@mycho.com',
    ];

    const PREMIUM_USERS = [
        'Rando Premium' => 'rando.premium@gmail.com',
    ];

    const REGULAR_USERS = [
        'xXPussy|DestroyerXx' => 'pussy.destroyer@gmail.com'
    ];

    const ROLES = [
        'GODMODE',
        'ADMIN',
        'PREMIUM',
        'REGULAR',
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

        //roles
        $roles = collect(self::ROLES)->mapWithKeys(
            fn (string $roleName) => [
                $roleName => Role::factory()->create(['name' => $roleName])
            ]
        );

        $this->createUsers(self::GOD_MODE_USERS, $avatars[0], $roles['GODMODE']);
        $this->createUsers(self::ADMINS, $avatars[1], $roles['ADMIN']);
        $this->createUsers(self::PREMIUM_USERS, $avatars[2], $roles['PREMIUM']);
        $this->createUsers(self::REGULAR_USERS, $avatars[3], $roles['REGULAR']);
    }

    private function createUsers(array $users, Avatar $avatar, Role $role): void
    {
        foreach ($users as $user => $email) {
            User::factory()->create([
                'name' => $user,
                'email' => $email,
                'avatar_id' => $avatar->id,
                'city_id' => City::first()->id,
                'role_id' => $role->id
            ]);
        }
    }
}
