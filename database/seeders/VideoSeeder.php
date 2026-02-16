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
    'title' => '
Dark Secrets Beneath a Perfect Life — Mystery Crime Movie 🎬 Water\'s Edge',
    'youtube_id' => 'tcFpWRX6tKM', // Exemple d'ID YouTube
    'description' => '

A novelist and his wife believe they live in a peaceful, ordinary town — until a series of disturbing discoveries pulls them into a hidden world of deception, desire, and violence. As buried secrets begin to surface, they realize that everyone around them may be hiding something dangerous. Trust erodes, paranoia grows, and the line between truth and illusion disappears.

This suspenseful crime thriller explores how far people will go to protect their secrets. With mounting tension, unexpected twists, and a chilling atmosphere, the story reveals the dark side of a seemingly perfect community where nothing is as innocent as it appears.
',
    'is_safe' => false
]);
    }
}
