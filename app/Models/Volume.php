<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volume extends Model
{
    protected $fillable = [
        'book_id',
        'number',
        'title',
        'page_start',
        'page_end',
    ];

    protected $casts = [
        'number' => 'integer',
        'page_start' => 'integer',
        'page_end' => 'integer',
    ];

    // العلاقات

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
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

    public function getDisplayNameAttribute(): string
    {
        if ($this->title) {
            return "الجزء {$this->number}: {$this->title}";
        }
        return "الجزء {$this->number}";
    }

    public function getPagesCountAttribute(): int
    {
        if ($this->page_start && $this->page_end) {
            return $this->page_end - $this->page_start + 1;
        }
        return $this->pages()->count();
    }
}
