<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'genre',
        'release_year',
        'episodes',
        'chapters',
    ];

    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
            'episodes' => 'integer',
            'chapters' => 'integer',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
