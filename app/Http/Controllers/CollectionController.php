<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CollectionController extends Controller
{
    public function index(User $user): UserResource
    {
        return new UserResource($user->load('media'));
    }

    public function store(User $user, Media $medium): JsonResponse
    {
        if ($user->media()->whereKey($medium->id)->exists()) {
            return response()->json([
                'message' => 'Media sudah ada di koleksi pengguna',
            ], 409);
        }

        $user->media()->attach($medium);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi',
            'media' => new MediaResource($medium),
        ], 201);
    }

    public function destroy(User $user, Media $medium): JsonResponse
    {
        if ($user->media()->detach($medium) === 0) {
            return response()->json([
                'message' => 'Media tidak ditemukan di koleksi pengguna',
            ], 404);
        }

        return response()->json(['message' => 'Media berhasil dihapus dari koleksi']);
    }
}
