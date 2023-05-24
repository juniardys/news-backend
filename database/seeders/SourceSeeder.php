<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // New York Times
        Source::create([
            'key' => 'nytimes',
            'name' => 'New York Times',
        ]);

        // The Guardian
        Source::create([
            'key' => 'guardian',
            'name' => 'The Guardian',
        ]);

        // News API
        Source::create([
            'key' => 'newsapi',
            'name' => 'News API',
        ]);
    }
}
