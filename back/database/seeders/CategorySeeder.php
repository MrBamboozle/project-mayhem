<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private const CATEGORIES = [
        "Sports",
        "Music",
        "Arts & Culture",
        "Technology",
        "Education",
        "Food & Drink",
        "Health & Wellness",
        "Business & Networking",
        "Charity & Causes",
        "Outdoor & Adventure"
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::CATEGORIES as $category) {
            Category::factory()->create(['name' => $category]);
        }
    }
}
