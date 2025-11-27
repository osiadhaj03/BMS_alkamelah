<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    protected $fillable = [
        'full_name',
        'slug',
        'biography',
        'image',
        'madhhab',
        'is_living',
        'birth_year_type',
        'birth_year',
        'death_year_type',
        'death_year',
        'birth_date',
        'death_date',
        'author_role',
    ];

    protected $casts = [
        'is_living' => 'boolean',
        'birth_year' => 'integer',
        'death_year' => 'integer',
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    // العلاقات

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'author_book')
            ->withPivot(['role', 'is_main', 'display_order'])
            ->withTimestamps();
    }

    // Accessors

    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }

    public function getLifespanAttribute(): ?string
    {
        if ($this->is_living) {
            return $this->birth_year ? "ولد {$this->birth_year}" : 'معاصر';
        }
        
        if ($this->birth_year && $this->death_year) {
            return "{$this->birth_year} - {$this->death_year}";
        }
        
        if ($this->death_year) {
            return "ت {$this->death_year}";
        }
        
        return null;
    }

    // Scopes

    public function scopeLiving($query)
    {
        return $query->where('is_living', true);
    }

    public function scopeDeceased($query)
    {
        return $query->where('is_living', false);
    }
}
