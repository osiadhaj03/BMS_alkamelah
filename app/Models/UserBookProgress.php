<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User Book Progress Model
 * 
 * تتبع تقدم القراءة للمستخدمين
 */
class UserBookProgress extends Model
{
    protected $table = 'user_book_progress';

    protected $fillable = [
        'user_id',
        'book_id',
        'last_page',
        'furthest_page',
        'total_visits',
        'total_reading_time',
        'last_read_at',
    ];

    protected $casts = [
        'last_page' => 'integer',
        'furthest_page' => 'integer',
        'total_visits' => 'integer',
        'total_reading_time' => 'integer',
        'last_read_at' => 'datetime',
    ];

    /**
     * المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الكتاب
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * نسبة التقدم
     */
    public function getProgressPercentageAttribute(): float
    {
        if (!$this->book) {
            return 0;
        }

        $totalPages = $this->book->pages()->count();
        if ($totalPages === 0) {
            return 0;
        }

        return round(($this->furthest_page / $totalPages) * 100, 1);
    }

    /**
     * وقت القراءة بالدقائق
     */
    public function getReadingTimeMinutesAttribute(): int
    {
        return (int) ceil($this->total_reading_time / 60);
    }
}
