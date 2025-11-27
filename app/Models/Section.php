<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // العلاقات

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'ososa');
    }

    // Accessors

    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }
}
