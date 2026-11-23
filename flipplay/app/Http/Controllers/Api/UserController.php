<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::query()
            ->orderBy('id')
            ->get();

        return UserResource::collection($users);
    }

    public function show(int $user): UserResource|JsonResponse
    {
        $foundUser = User::find($user);

        if (! $foundUser) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return new UserResource($foundUser->load('media'));
    }
}
