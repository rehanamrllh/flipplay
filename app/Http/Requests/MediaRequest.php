<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $media = $this->route('medium');

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($media),
            ],
            'type' => ['required', 'in:Anime,Manga'],
            'genre' => ['required', 'string', 'max:100'],
            'release_year' => ['required', 'integer', 'min:1900', 'max:' . now()->year],
            'episodes' => ['required_if:type,Anime', 'prohibited_if:type,Manga', 'integer', 'min:1'],
            'chapters' => ['required_if:type,Manga', 'prohibited_if:type,Anime', 'integer', 'min:1'],
        ];
    }
}
