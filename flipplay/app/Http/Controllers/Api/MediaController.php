<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $media = Media::query()
            ->when($request->filled('title'), function ($query) use ($request): void {
                $query->where('title', 'like', '%'.$request->string('title').'%');
            })
            ->latest()
            ->get();

        return MediaResource::collection($media);
    }

    public function store(StoreMediaRequest $request): MediaResource
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media->load('users')))
            ->additional(['message' => 'Media created successfully']);
    }

    public function show(int $medium): MediaResource|JsonResponse
    {
        $media = Media::find($medium);

        if (! $media) {
            return response()->json([
                'message' => 'Media not found',
            ], 404);
        }

        return new MediaResource($media->load('users'));
    }

    public function update(UpdateMediaRequest $request, int $medium): MediaResource|JsonResponse
    {
        $media = Media::find($medium);

        if (! $media) {
            return response()->json([
                'message' => 'Media not found',
            ], 404);
        }

        $media->update($request->validated());

        return (new MediaResource($media->load('users')))
            ->additional(['message' => 'Media updated successfully']);
    }

    public function destroy(int $medium): JsonResponse
    {
        $media = Media::find($medium);

        if (! $media) {
            return response()->json([
                'message' => 'Media not found',
            ], 404);
        }

        $media->delete();

        return response()->json([
            'message' => 'Media deleted successfully',
        ], 200);
    }

    public function attach(int $medium, int $user): JsonResponse
    {
        $media = Media::find($medium);
        $pengguna = User::find($user);

        if (! $media || ! $pengguna) {
            return response()->json([
                'message' => 'Media or user not found',
            ], 404);
        }

        if ($media->users()->whereKey($pengguna->id)->exists()) {
            return response()->json([
                'message' => 'Media is already in this user collection',
            ], 409);
        }

        $media->users()->attach($pengguna->id);

        return response()->json([
            'message' => 'Media added to collection',
            'data' => new MediaResource($media->load('users')),
        ], 200);
    }

    public function detach(int $medium, int $user): JsonResponse
    {
        $media = Media::find($medium);
        $pengguna = User::find($user);

        if (! $media || ! $pengguna) {
            return response()->json([
                'message' => 'Media or user not found',
            ], 404);
        }

        if (! $media->users()->whereKey($pengguna->id)->exists()) {
            return response()->json([
                'message' => 'Media is not in this user collection',
            ], 404);
        }

        $media->users()->detach($pengguna->id);

        return response()->json([
            'message' => 'Media removed from collection',
        ], 200);
    }

    public function userMedia(User $user): JsonResponse
    {
        $media = $user->media()->latest()->get();

        return response()->json([
            'data' => MediaResource::collection($media),
        ]);
    }
}
