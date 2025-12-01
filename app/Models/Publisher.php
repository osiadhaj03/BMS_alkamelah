<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publisher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'email',
        'phone',
        'description',
        'website_url',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * الكتب التابعة لهذا الناشر
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
        return $this->hasMany(BookExtractedMetadata::class, 'matched_publisher_id');
    }

    /**
     * Scope للناشرين النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على عدد الكتب
     */
    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }
}
