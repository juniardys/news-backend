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
            'key' => 'nyt',
            'name' => 'New York Times',
            'type' => 'newyorktimes',
        ]);
        Source::create([
            'key' => 'inyt',
            'name' => 'International New York Times',
            'type' => 'newyorktimes',
        ]);

        // The Guardian
        Source::create([
            'key' => 'theguardian',
            'name' => 'The Guardian',
            'type' => 'theguardian',
        ]);
    }
}
