<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('media', 'title')->ignore($this->route('medium'))],
            'type' => ['sometimes', 'required', 'string', 'max:100'],
            'genre' => ['sometimes', 'required', 'string', 'max:100'],
            'release_year' => ['sometimes', 'required', 'integer', 'min:1888', 'max:2100'],
            'episodes' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'chapters' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }
}
