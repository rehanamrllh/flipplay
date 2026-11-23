<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_can_be_created_and_listed(): void
    {
        $response = $this->postJson('/api/media', [
            'title' => 'Frieren',
            'type' => 'Anime',
            'genre' => 'Adventure',
            'release_year' => 2023,
            'episodes' => 28,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Frieren');

        $this->getJson('/api/media')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_media_search_and_show(): void
    {
        $media = Media::create([
            'title' => 'Naruto',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 2002,
            'episodes' => 220,
        ]);

        $this->getJson('/api/media?search=Naruto')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson("/api/media/{$media->id}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Naruto');
    }

    public function test_media_can_be_patched_and_updated(): void
    {
        $media = Media::create([
            'title' => 'Bleach',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 2004,
            'episodes' => 366,
        ]);

        $response = $this->patchJson("/api/media/{$media->id}", [
            'title' => 'Bleach Updated',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 2004,
            'episodes' => 366,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Bleach Updated');
    }

    public function test_media_can_be_deleted(): void
    {
        $media = Media::create([
            'title' => 'Temp Media',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 2020,
            'episodes' => 12,
        ]);

        $this->deleteJson("/api/media/{$media->id}")
            ->assertOk();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_user_collection_endpoints(): void
    {
        $user = User::factory()->create();
        $media = Media::create([
            'title' => 'One Piece',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 1999,
            'episodes' => 1155,
        ]);

        // POST collection (add)
        $this->postJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertCreated();

        // GET collection (index)
        $this->getJson("/api/users/{$user->id}/collection")
            ->assertOk()
            ->assertJsonPath('data.media.0.title', 'One Piece');

        // DELETE collection (remove)
        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertOk();

        // DELETE collection non-existent (404)
        $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertNotFound();
    }

    public function test_duplicate_media_in_a_collection_is_rejected(): void
    {
        $user = User::factory()->create();
        $media = Media::create([
            'title' => 'Death Note',
            'type' => 'Anime',
            'genre' => 'Thriller',
            'release_year' => 2006,
            'episodes' => 37,
        ]);

        $this->postJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertCreated();

        $this->postJson("/api/users/{$user->id}/collection/{$media->id}")
            ->assertConflict();
    }

    public function test_invalid_media_is_rejected(): void
    {
        $this->postJson('/api/media', [
            'title' => '',
            'type' => 'Podcast',
        ])->assertUnprocessable();
    }

    public function test_media_count_field_must_match_its_type(): void
    {
        $this->postJson('/api/media', [
            'title' => 'Manga Without Chapters',
            'type' => 'Manga',
            'genre' => 'Action',
            'release_year' => 2023,
            'episodes' => 12,
        ])->assertUnprocessable();

        $this->postJson('/api/media', [
            'title' => 'Anime Without Episodes',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 2023,
            'chapters' => 12,
        ])->assertUnprocessable();
    }
}
