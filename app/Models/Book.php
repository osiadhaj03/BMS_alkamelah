<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'shamela_id',
        'title',
        'description',
        'visibility',
        'book_section_id',
        'publisher_id',
        'has_original_pagination',
        'additional_notes',
    ];

    protected $casts = [
        'has_original_pagination' => 'boolean',
    ];

    /**
     * خيارات الرؤية
     */
    const VISIBILITY = [
        'public' => 'عام',
        'private' => 'خاص',
        'restricted' => 'مقيد',
    ];

    /**
     * قسم الكتاب
     */
    public function bookSection(): BelongsTo
    {
        return $this->belongsTo(BookSection::class);
    }

    /**
     * الناشر
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    /**
     * المؤلفين (many-to-many)
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_book')
            ->withPivot(['role', 'is_main', 'display_order'])
            ->withTimestamps()
            ->orderByPivot('display_order');
    }

    /**
     * المؤلف الرئيسي
     */
    public function mainAuthor(): BelongsToMany
    {
        return $this->authors()->wherePivot('is_main', true);
    }

    /**
     * المؤلفين فقط (بدون محققين ومترجمين)
     */
    public function bookAuthors(): BelongsToMany
    {
        return $this->authors()->wherePivot('role', 'author');
    }

    /**
     * المحققين
     */
    public function editors(): BelongsToMany
    {
        return $this->authors()->wherePivot('role', 'editor');
    }

    /**
     * المترجمين
     */
    public function translators(): BelongsToMany
    {
        return $this->authors()->wherePivot('role', 'translator');
    }

    /**
     * المجلدات
     */
    public function volumes(): HasMany
    {
        return $this->hasMany(Volume::class)->orderBy('number');
    }

    /**
     * الفصول
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
     * الصفحات
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class)->orderBy('page_number');
    }

    /**
     * البيانات المستخرجة
     */
    public function extractedMetadata(): HasOne
    {
        return $this->hasOne(BookExtractedMetadata::class);
    }

    /**
     * علاقات المؤلفين والكتب (Pivot)
     */
    public function authorBooks(): HasMany
    {
        return $this->hasMany(AuthorBook::class);
    }

    /**
     * Scope للكتب العامة
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Scope للكتب الخاصة
     */
    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    /**
     * Scope للكتب المقيدة
     */
    public function scopeRestricted($query)
    {
        return $query->where('visibility', 'restricted');
    }

    /**
     * Scope للكتب المستوردة من الشاملة
     */
    public function scopeFromShamela($query)
    {
        return $query->whereNotNull('shamela_id');
    }

    /**
     * الحصول على عدد المجلدات
     */
    public function getVolumesCountAttribute(): int
    {
        return $this->volumes()->count();
    }

    /**
     * الحصول على عدد الصفحات
     */
    public function getPagesCountAttribute(): int
    {
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
     * التحقق من وجود بيانات مستخرجة
     */
    public function hasExtractedMetadata(): bool
    {
        return $this->extractedMetadata()->exists();
    }
    /**
     * البيانات الوصفية للكتاب (Metadata)
     */
    public function bookMetadata(): HasOne
    {
        return $this->hasOne(BookMetadata::class);
    }
}
