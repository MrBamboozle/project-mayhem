<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    const ROLES = [
        'GODMODE',
        'ADMIN',
        'PREMIUM',
        'REGULAR',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = self::ROLES;

        foreach ($cases as $role) {
            Role::factory(['name' => $role])->createOne();
        }
    }
}
