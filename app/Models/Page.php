<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'volume_id',
        'chapter_id',
        'page_number',
        'internal_index',
        'part',
        'content',
        'original_page_number',
        'html_content',
    ];

    protected $casts = [
        'page_number' => 'integer',
        'original_page_number' => 'integer',
    ];

    /**
     * الكتاب
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * المجلد
     */
    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }

    /**
     * الفصل
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * الحصول على المحتوى للعرض
     */
    public function getDisplayContentAttribute(): string
    {
        return $this->html_content ?? $this->content ?? '';
    }

    /**
     * الحصول على رقم الصفحة للعرض
     */
    public function getDisplayPageNumberAttribute(): string
    {
        if ($this->original_page_number) {
            return "{$this->page_number} (أصلي: {$this->original_page_number})";
        }
        return (string) $this->page_number;
    }

    /**
     * التحقق من وجود محتوى HTML
     */
    public function hasHtmlContent(): bool
    {
        return !empty($this->html_content);
    }

    /**
     * التحقق من وجود ترقيم أصلي
     */
    public function hasOriginalPageNumber(): bool
    {
        return !is_null($this->original_page_number);
    }

    /**
     * Scope للبحث في المحتوى
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('content', 'LIKE', "%{$term}%")
              ->orWhere('html_content', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Scope حسب الجزء
     */
    public function scopeByPart($query, string $part)
    {
        return $query->where('part', $part);
    }

    /**
     * الحصول على الصفحة التالية
     */
    public function getNextPage(): ?Page
    {
        return static::where('book_id', $this->book_id)
            ->where('page_number', '>', $this->page_number)
            ->orderBy('page_number')
            ->first();
    }

    /**
     * الحصول على الصفحة السابقة
     */
    public function getPreviousPage(): ?Page
    {
        return static::where('book_id', $this->book_id)
            ->where('page_number', '<', $this->page_number)
            ->orderBy('page_number', 'desc')
            ->first();
    }
}
