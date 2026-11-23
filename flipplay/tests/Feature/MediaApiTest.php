<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_crud(): void
    {
        $create = $this->postJson('/api/media', [
            'title' => 'Interstellar',
            'type' => 'Anime',
            'genre' => 'Science Fiction',
            'release_year' => 2014,
            'episodes' => 24,
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.title', 'Interstellar');

        $media = Media::where('title', 'Interstellar')->firstOrFail();

        $this->getJson("/api/media/{$media->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $media->id)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'type', 'genre', 'release_year', 'episodes', 'chapters', 'users'],
            ]);

        $this->patchJson("/api/media/{$media->id}", [
            'genre' => 'drama',
        ])->assertOk()
            ->assertJsonPath('data.genre', 'drama');

        $this->deleteJson("/api/media/{$media->id}")
            ->assertOk();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_empty_title_is_rejected(): void
    {
        $this->postJson('/api/media', [
            'title' => '',
            'type' => 'Anime',
            'genre' => 'Action',
            'release_year' => 2024,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    public function test_duplicate_title_is_rejected(): void
    {
        Media::factory()->create(['title' => 'Dune']);

        $this->postJson('/api/media', [
            'title' => 'Dune',
            'type' => 'Manga',
            'genre' => 'Science Fiction',
            'release_year' => 1985,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    public function test_media_can_be_attached_once_and_detached(): void
    {
        $media = Media::factory()->create();
        $user = User::factory()->create();

        $this->postJson("/api/media/{$media->id}/users/{$user->id}")
            ->assertOk();

        $this->postJson("/api/media/{$media->id}/users/{$user->id}")
            ->assertStatus(409);

        $this->getJson("/api/users/{$user->id}/media")
            ->assertOk()
            ->assertJsonPath('data.0.id', $media->id);

        $this->deleteJson("/api/media/{$media->id}/users/{$user->id}")
            ->assertOk();

        $this->assertDatabaseMissing('media_user', [
            'media_id' => $media->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_missing_media_returns_not_found(): void
    {
        $this->getJson('/api/media/999999')
            ->assertNotFound()
            ->assertJson([
                'message' => 'Media not found',
            ]);
    }

    public function test_deleting_missing_media_returns_not_found(): void
    {
        $this->deleteJson('/api/media/999999')
            ->assertNotFound()
            ->assertJson([
                'message' => 'Media not found',
            ]);
    }

    public function test_detaching_missing_collection_relation_returns_not_found(): void
    {
        $media = Media::factory()->create();
        $user = User::factory()->create();

        $this->deleteJson("/api/media/{$media->id}/users/{$user->id}")
            ->assertNotFound()
            ->assertJson([
                'message' => 'Media is not in this user collection',
            ]);
    }

    public function test_index_lists_media_without_users_but_detail_loads_users(): void
    {
        $media = Media::factory()->create();
        $user = User::factory()->create();
        $media->users()->attach($user);

        $this->getJson('/api/media')
            ->assertOk()
            ->assertJsonMissingPath('data.0.users');

        $this->getJson("/api/media/{$media->id}")
            ->assertOk()
            ->assertJsonPath('data.users.0.id', $user->id);
    }

    public function test_users_endpoint_returns_rehan_and_aceng(): void
    {
        User::factory()->create(['name' => 'Rehan']);
        User::factory()->create(['name' => 'Aceng']);

        $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonMissingPath('data.0.media');
    }

    public function test_user_detail_returns_collection_media(): void
    {
        $user = User::factory()->create(['name' => 'Rehan']);
        $media = Media::factory()->create(['title' => 'One Piece']);
        $user->media()->attach($media);

        $this->getJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Rehan')
            ->assertJsonPath('data.media.0.title', 'One Piece');
    }

    public function test_missing_user_detail_returns_not_found(): void
    {
        $this->getJson('/api/users/999999')
            ->assertNotFound()
            ->assertJson([
                'message' => 'User not found',
            ]);
    }
}
