<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'shamela_id',
        'title',
        'description',
        'slug',
        'cover_image',
        'pages_count',
        'volumes_count',
        'status',
        'visibility',
        'source_url',
        'book_section_id',
        'publisher_id',
        'edition_DATA',
        'edition',
        'edition_number',
        'has_original_pagination',
        'publication_year',
        'publication_place',
        'volume_count',
        'page_count',
        'isbn',
        'author_death_year',
        'author_role',
        'edition_info',
        'additional_notes',
    ];

    protected $casts = [
        'has_original_pagination' => 'boolean',
        'pages_count' => 'integer',
        'volumes_count' => 'integer',
        'volume_count' => 'integer',
        'page_count' => 'integer',
    ];

    // العلاقات

    public function section(): BelongsTo
    {
        return $this->belongsTo(BookSection::class, 'book_section_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_book')
            ->withPivot(['role', 'is_main', 'display_order'])
            ->withTimestamps();
    }

    public function volumes(): HasMany
    {
        return $this->hasMany(Volume::class)->orderBy('number');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('page_number');
    }

    // Accessors

    public function getMainAuthorAttribute(): ?Author
    {
        return $this->authors()->wherePivot('is_main', true)->first();
    }

    // Scopes

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }
}
