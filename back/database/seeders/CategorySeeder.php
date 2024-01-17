<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    const EVENT_CATEGORY_NAMES = [
        'Tes',
        'Test',
        'Test',
        'Test',
        'Test',
        'Test',
        'Test',
        'Test',
        'Test',
        'Test',
        'Tes',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::EVENT_CATEGORY_NAMES as $key => $name)
        Category::factory()->create(['name' => $name.$key]);
    }
}
