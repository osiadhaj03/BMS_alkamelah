<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'volume_id',
        'book_id',
        'title',
        'parent_id',
        'level',
        'order',
        'page_start',
        'page_end',
    ];

    protected $casts = [
        'level' => 'integer',
        'order' => 'integer',
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
     * المجلد
     */
    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }

    /**
     * الفصل الأب
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'parent_id');
    }

    /**
     * الفصول الفرعية
     */
    public function children(): HasMany
    {
        return $this->hasMany(Chapter::class, 'parent_id')->orderBy('order');
    }

    /**
     * جميع الفصول الفرعية (متداخلة)
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    /**
     * الصفحات في هذا الفصل
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('page_number');
    }

    /**
     * التحقق من أن الفصل رئيسي (ليس له أب)
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * التحقق من وجود فصول فرعية
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Scope للفصول الرئيسية
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope حسب المستوى
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope للترتيب
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
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
     * الحصول على المسار الكامل للفصل
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->title];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->title);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }
}
