<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $rehan = User::create([
            'name' => 'Rehan',
            'email' => 'rehan@example.com',
            'password' => 'password',
        ]);

        $aceng = User::create([
            'name' => 'Aceng',
            'email' => 'aceng@example.com',
            'password' => 'password',
        ]);

        $onePiece = Media::create([
            'title' => 'One Piece',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 1999,
            'episodes' => 1155,
        ]);

        $chainsawMan = Media::create([
            'title' => 'Chainsaw Man',
            'type' => 'Manga',
            'genre' => 'Action',
            'release_year' => 2018,
            'chapters' => 145,
        ]);

        $jujutsuKaisen = Media::create([
            'title' => 'Jujutsu Kaisen',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 2020,
            'episodes' => 24,
        ]);

        $rehan->media()->attach([$onePiece->id, $chainsawMan->id, $jujutsuKaisen->id]);
        $aceng->media()->attach([$onePiece->id, $jujutsuKaisen->id]);
    }
}
