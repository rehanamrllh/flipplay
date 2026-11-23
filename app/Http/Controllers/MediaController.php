<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $media = Media::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $query->where('title', 'like', '%' . $request->string('search') . '%');
            })
            ->with('users')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return MediaResource::collection($media);
    }

    public function store(MediaRequest $request): MediaResource
    {
        return new MediaResource(Media::create($request->validated()));
    }

    public function show(Media $medium): MediaResource
    {
        return new MediaResource($medium->load('users'));
    }

    public function update(MediaRequest $request, Media $medium): MediaResource
    {
        $medium->update($request->validated());

        return new MediaResource($medium->load('users'));
    }

    public function destroy(Media $medium)
    {
        $medium->delete();

        return response()->json(['message' => 'Media berhasil dihapus']);
    }
}
