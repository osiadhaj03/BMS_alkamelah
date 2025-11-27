<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookSection extends Model
{
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'sort_order',
        'is_active',
        'slug',
        'logo_path',
        'icon_type',
        'icon_url',
        'icon_name',
        'icon_color',
        'icon_size',
        'icon_custom_size',
        'icon_library',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'icon_custom_size' => 'integer',
    ];

    // العلاقات

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BookSection::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(BookSection::class, 'parent_id')->orderBy('sort_order');
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    // Accessors

    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }

    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
