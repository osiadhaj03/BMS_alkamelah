<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AuthorBook extends Pivot
{
    use HasFactory;

    protected $table = 'author_book';

    public $incrementing = true;

    protected $fillable = [
        'book_id',
        'author_id',
        'role',
        'is_main',
        'display_order',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * الأدوار المتاحة
     */
    const ROLES = [
        'author' => 'مؤلف',
        'co_author' => 'مؤلف مشارك',
        'editor' => 'محقق',
        'translator' => 'مترجم',
        'reviewer' => 'مراجع',
        'commentator' => 'معلق',
    ];

    /**
     * الكتاب
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * المؤلف
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * الحصول على اسم الدور بالعربية
     */
    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    /**
     * Scope للمؤلفين الرئيسيين
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    /**
     * Scope حسب الدور
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
