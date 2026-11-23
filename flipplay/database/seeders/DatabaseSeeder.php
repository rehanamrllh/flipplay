<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $rehan = User::factory()->create([
            'name' => 'Rehan',
            'email' => 'rehan@example.com',
        ]);
        $aceng = User::factory()->create([
            'name' => 'Aceng',
            'email' => 'aceng@example.com',
        ]);

        $onePiece = Media::create([
            'title' => 'One Piece',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 1999,
            'episodes' => 1155,
        ]);
        $frieren = Media::create([
            'title' => "Frieren: Beyond Journey's End",
            'type' => 'Anime',
            'genre' => 'Adventure',
            'release_year' => 2023,
            'episodes' => 28,
        ]);
        $chainsawMan = Media::create([
            'title' => 'Chainsaw Man',
            'type' => 'Manga',
            'genre' => 'Action',
            'release_year' => 2018,
            'chapters' => 145,
        ]);
        $spyXFamily = Media::create([
            'title' => 'Spy x Family',
            'type' => 'Manga',
            'genre' => 'Comedy',
            'release_year' => 2019,
            'chapters' => 110,
        ]);

        $rehan->media()->attach([$onePiece->id, $chainsawMan->id]);
        $aceng->media()->attach([$frieren->id, $spyXFamily->id]);
    }
}
