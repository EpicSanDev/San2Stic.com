<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sound;

class SoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sound::create([
            'name' => 'Son 1: Ambiance de parc',
            'path' => 'sounds/park.mp3',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        Sound::create([
            'name' => 'Son 2: Marché animé',
            'path' => 'sounds/market.mp3',
            'latitude' => 48.8600,
            'longitude' => 2.3550,
        ]);

        Sound::create([
            'name' => 'Son 3: Musique de rue',
            'path' => 'sounds/music.mp3',
            'latitude' => 48.8530,
            'longitude' => 2.3480,
        ]);
    }
}