<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Video::create([
    'title' => 'L\'histoire de Noé',
    'youtube_id' => 'xsC2eSpnNKk', // Exemple d'ID YouTube
    'description' => 'Un dessin animé sur l\'arche.',
    'is_safe' => true
]);
    }
}
