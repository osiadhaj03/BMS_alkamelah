<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publisher extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'address',
        'email',
        'phone',
        'description',
        'website_url',
        'image',
        'is_active',
        'city',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // العلاقات

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    // Accessors

    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
