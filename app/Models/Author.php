<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'laqab',
        'kunyah',
        'biography',
        'video_links',
        'image',
        'madhhab',
        'is_living',
        'birth_date',
        'death_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'is_living' => 'boolean',
        'video_links' => 'array',
    ];

    /**
     * المذاهب المتاحة
     */
    const MADHAHIB = [
        'المذهب الحنفي' => 'المذهب الحنفي',
        'المذهب المالكي' => 'المذهب المالكي',
        'المذهب الشافعي' => 'المذهب الشافعي',
        'المذهب الحنبلي' => 'المذهب الحنبلي',
        'آخرون' => 'آخرون',
    ];

    /**
     * العلاقة مع الكتب (many-to-many)
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'author_book')
            ->withPivot(['role', 'is_main', 'display_order'])
            ->withTimestamps();
    }

    /**
     * الكتب كمؤلف رئيسي
     */
    public function mainBooks(): BelongsToMany
    {
        return $this->books()->wherePivot('is_main', true);
    }

    /**
     * الكتب كمؤلف
     */
    public function authoredBooks(): BelongsToMany
    {
        return $this->books()->wherePivot('role', 'author');
    }

    /**
     * الكتب كمترجم
     */
    public function translatedBooks(): BelongsToMany
    {
        return $this->books()->wherePivot('role', 'translator');
    }

    /**
     * الكتب كمحقق
     */
    public function editedBooks(): BelongsToMany
    {
        return $this->books()->wherePivot('role', 'editor');
    }

    /**
     * البيانات المستخرجة المطابقة كمؤلف
     */
    public function extractedMetadataAsAuthor(): HasMany
    {
        return $this->hasMany(BookExtractedMetadata::class, 'matched_author_id');
    }

    /**
     * البيانات المستخرجة المطابقة كمحقق
     */
    public function extractedMetadataAsTahqeeq(): HasMany
    {
        return $this->hasMany(BookExtractedMetadata::class, 'matched_tahqeeq_author_id');
    }

    /**
     * Scope للمؤلفين الأحياء
     */
    public function scopeLiving($query)
    {
        return $query->where('is_living', true);
    }

    /**
     * Scope للمؤلفين المتوفين
     */
    public function scopeDeceased($query)
    {
        return $query->where('is_living', false);
    }

    /**
     * Scope حسب المذهب
     */
    public function scopeByMadhhab($query, string $madhhab)
    {
        return $query->where('madhhab', $madhhab);
    }

    /**
     * الحصول على عدد الكتب
     */
    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }

    /**
     * الحصول على الاسم الكامل (مركب من جميع أجزاء الاسم)
     */
    public function getFullNameAttribute(): string
    {
        $nameParts = array_filter([
            $this->laqab,
            $this->kunyah,
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $nameParts);
    }
}
