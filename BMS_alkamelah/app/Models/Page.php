<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    protected $fillable = [
        'book_id',
        'volume_id',
        'chapter_id',
        'page_number',
        'internal_index',
        'part',
        'content',
        'original_page_number',
        'word_count',
        'html_content',
        'printed_missing',
        'formatted_content',
    ];

    protected $casts = [
        'page_number' => 'integer',
        'original_page_number' => 'integer',
        'word_count' => 'integer',
        'printed_missing' => 'boolean',
    ];

    // العلاقات

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    // Accessors

    public function getDisplayContentAttribute(): string
    {
        return $this->formatted_content ?? $this->html_content ?? $this->content ?? '';
    }

    public function getPageLabelAttribute(): string
    {
        if ($this->part) {
            return "ج{$this->part} ص{$this->page_number}";
        }
        return "ص{$this->page_number}";
    }

    // Scopes

    public function scopeWithContent($query)
    {
        return $query->whereNotNull('content')->where('content', '!=', '');
    }

    public function scopeMissing($query)
    {
        return $query->where('printed_missing', true);
    }
}
