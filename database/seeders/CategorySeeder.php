<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['key' => 'business', 'name' => 'Business'],
            ['key' => 'entertainment', 'name' => 'Entertainment'],
            ['key' => 'general', 'name' => 'General'],
            ['key' => 'health', 'name' => 'Health'],
            ['key' => 'science', 'name' => 'Science'],
            ['key' => 'sports', 'name' => 'Sports'],
            ['key' => 'technology', 'name' => 'Technology'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
