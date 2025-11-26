<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $fillable = [
        'volume_id',
        'book_id',
        'title',
        'parent_id',
        'level',
        'order',
        'page_start',
        'page_end',
        'estimated_reading_time',
        'internal_index_start',
        'internal_index_end',
        'start_page_internal_index',
        'end_page_internal_index',
    ];

    protected $casts = [
        'level' => 'integer',
        'order' => 'integer',
        'page_start' => 'integer',
        'page_end' => 'integer',
        'estimated_reading_time' => 'integer',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Chapter::class, 'parent_id')->orderBy('order');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('page_number');
    }

    // Accessors

    public function getPagesCountAttribute(): int
    {
        if ($this->page_start && $this->page_end) {
            return $this->page_end - $this->page_start + 1;
        }
        return $this->pages()->count();
    }

    public function getReadingTimeAttribute(): string
    {
        if ($this->estimated_reading_time) {
            $hours = floor($this->estimated_reading_time / 60);
            $minutes = $this->estimated_reading_time % 60;
            
            if ($hours > 0) {
                return "{$hours} ساعة و {$minutes} دقيقة";
            }
            return "{$minutes} دقيقة";
        }
        return 'غير محدد';
    }

    // Scopes

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }
}
