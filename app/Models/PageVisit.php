<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PageVisit extends Model
{
    protected $fillable = [
        'session_id',
        'ip_address',
        'url',
        'route_name',
        'page_title',
        'duration_seconds',
        'is_bot',
        'bot_name',
        'referer',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'is_bot' => 'boolean',
        'duration_seconds' => 'integer',
    ];

    // ==================== Scopes ====================

    public function scopeHumans($query)
    {
        return $query->where('is_bot', false);
    }

    public function scopeBots($query)
    {
        return $query->where('is_bot', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year);
    }

    // ==================== إحصائيات عامة ====================

    /** إجمالي الزيارات (بدون Bots) */
    public static function totalHumanVisits(): int
    {
        return static::humans()->count();
    }

    /** عدد الزوار الفريدين حسب IP */
    public static function uniqueVisitorsCount(): int
    {
        return static::humans()->distinct('ip_address')->count('ip_address');
    }

    /** زيارات اليوم */
    public static function todayVisits(): int
    {
        return static::humans()->today()->count();
    }

    /** زيارات الشهر */
    public static function thisMonthVisits(): int
    {
        return static::humans()->thisMonth()->count();
    }

    /** متوسط الوقت في الصفحة (بالثواني) */
    public static function avgDuration(): float
    {
        return round(
            static::humans()
                ->whereNotNull('duration_seconds')
                ->where('duration_seconds', '>', 0)
                ->avg('duration_seconds') ?? 0,
            1
        );
    }

    /** تنسيق الثواني إلى دقائق:ثواني */
    public static function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' ثانية';
        }

        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;

        return $minutes . ':' . str_pad($secs, 2, '0', STR_PAD_LEFT) . ' دقيقة';
    }

    // ==================== إحصائيات الجلسات ====================

    /** عدد الجلسات الفريدة */
    public static function uniqueSessionsCount(): int
    {
        return static::humans()
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');
    }

    /** متوسط عدد الصفحات في الجلسة */
    public static function avgPagesPerSession(): float
    {
        $totalPages = static::humans()->count();
        $totalSessions = static::uniqueSessionsCount();

        return $totalSessions > 0
            ? round($totalPages / $totalSessions, 1)
            : 0;
    }

    /** أكثر الصفحات زيارة */
    public static function topPages(int $limit = 10)
    {
        return static::humans()
            ->select('route_name', 'page_title')
            ->selectRaw('COUNT(*) as visits_count')
            ->selectRaw('ROUND(AVG(CASE WHEN duration_seconds > 0 THEN duration_seconds END), 0) as avg_duration')
            ->groupBy('route_name', 'page_title')
            ->orderByDesc('visits_count')
            ->limit($limit)
            ->get();
    }

    // ==================== بيانات الجلسات ====================

    /** الحصول على جلسات الزوار مجمّعة */
    public static function sessionsSummary(int $limit = 50)
    {
        return static::humans()
            ->whereNotNull('session_id')
            ->select('session_id', 'ip_address')
            ->selectRaw('COUNT(*) as pages_count')
            ->selectRaw('SUM(COALESCE(duration_seconds, 0)) as total_duration')
            ->selectRaw('MIN(visited_at) as session_start')
            ->selectRaw('MAX(visited_at) as session_end')
            ->groupBy('session_id', 'ip_address')
            ->orderByDesc('session_start')
            ->limit($limit)
            ->get();
    }
}
