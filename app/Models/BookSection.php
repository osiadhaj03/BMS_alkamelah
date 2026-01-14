<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'sort_order',
        'is_active',
        'logo_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * القسم الأب
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BookSection::class, 'parent_id');
    }

    /**
     * الأقسام الفرعية
     */
    public function children(): HasMany
    {
        return $this->hasMany(BookSection::class, 'parent_id');
    }

    /**
     * جميع الأقسام الفرعية (متداخلة)
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    /**
     * الكتب في هذا القسم
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * البيانات المستخرجة المطابقة
     */
    public function extractedMetadata(): HasMany
    {
        return $this->hasMany(BookExtractedMetadata::class, 'matched_section_id');
    }

    /**
     * التحقق من أن القسم رئيسي (ليس له أب)
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Scope للأقسام النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للأقسام الرئيسية
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope للترتيب
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get sections with book count for homepage
     */
    public static function getForHomepage($limit = 6)
    {
        return self::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('books')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the URL for the section logo
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo_path)) {
            return asset('storage/' . $this->logo_path);
        }

        return asset('images/group1.svg');
    }
}
