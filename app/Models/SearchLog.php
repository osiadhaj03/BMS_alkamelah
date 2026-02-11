<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    protected $fillable = [
        'query',
        'search_type',
        'search_mode',
        'word_order',
        'word_match',
        'results_count',
        'page_visit_id',
        'ip_address',
        'filters',
    ];

    protected $casts = [
        'filters' => 'array',
        'results_count' => 'integer',
    ];

    // ==================== العلاقات ====================

    public function pageVisit()
    {
        return $this->belongsTo(PageVisit::class);
    }

    // ==================== Scopes ====================

    public function scopeOfType($query, string $type)
    {
        return $query->where('search_type', $type);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    // ==================== إحصائيات ====================

    public static function totalSearches(): int
    {
        return static::count();
    }

    public static function todaySearches(): int
    {
        return static::today()->count();
    }

    public static function topSearches(int $limit = 10)
    {
        return static::select('query', 'search_type')
            ->selectRaw('COUNT(*) as search_count')
            ->groupBy('query', 'search_type')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();
    }

    public static function typeDistribution(): array
    {
        $counts = static::selectRaw('search_type, COUNT(*) as count')
            ->groupBy('search_type')
            ->pluck('count', 'search_type')
            ->toArray();

        return [
            'books'   => $counts['books'] ?? 0,
            'authors' => $counts['authors'] ?? 0,
            'content' => $counts['content'] ?? 0,
        ];
    }

    public static function zeroResultSearches(int $limit = 10)
    {
        return static::where('results_count', 0)
            ->select('query', 'search_type')
            ->selectRaw('COUNT(*) as times_searched')
            ->groupBy('query', 'search_type')
            ->orderByDesc('times_searched')
            ->limit($limit)
            ->get();
    }
}
