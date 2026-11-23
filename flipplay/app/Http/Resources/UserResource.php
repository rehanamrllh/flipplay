<?php

namespace App\Http\Resources;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'media' => $this->whenLoaded('media', fn (): array => $this->media->map(
                fn (Media $media): array => [
                    'id' => $media->id,
                    'title' => $media->title,
                    'type' => $media->type,
                    'genre' => $media->genre,
                    'release_year' => $media->release_year,
                    'episodes' => $media->episodes,
                    'chapters' => $media->chapters,
                ]
            )->all()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
