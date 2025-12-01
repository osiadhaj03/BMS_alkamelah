<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volume extends Model
{
    use HasFactory;

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

    /**
     * الكتاب
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * الفصول في هذا المجلد
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    /**
     * الفصول الرئيسية فقط
     */
    public function rootChapters(): HasMany
    {
        return $this->chapters()->whereNull('parent_id')->orderBy('order');
    }

    /**
     * الصفحات في هذا المجلد
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('page_number');
    }

    /**
     * الحصول على عدد الصفحات
     */
    public function getPagesCountAttribute(): int
    {
        if ($this->page_start && $this->page_end) {
            return $this->page_end - $this->page_start + 1;
        }
        return $this->pages()->count();
    }

    /**
     * الحصول على عدد الفصول
     */
    public function getChaptersCountAttribute(): int
    {
        return $this->chapters()->count();
    }

    /**
     * الحصول على اسم المجلد للعرض
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->title) {
            return "المجلد {$this->number}: {$this->title}";
        }
        return "المجلد {$this->number}";
    }
}
