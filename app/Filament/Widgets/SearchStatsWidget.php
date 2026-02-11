<?php

namespace App\Filament\Widgets;

use App\Models\SearchLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SearchStatsWidget extends BaseWidget
{
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $distribution = SearchLog::typeDistribution();
        $topSearch = SearchLog::topSearches(1)->first();

        return [
            Stat::make('إجمالي عمليات البحث', number_format(SearchLog::totalSearches()))
                ->description('كل الأوقات')
                ->descriptionIcon('heroicon-o-magnifying-glass')
                ->color('primary'),

            Stat::make('بحث اليوم', number_format(SearchLog::todaySearches()))
                ->description('منذ بداية اليوم')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),

            Stat::make('أكثر بحث تكراراً', $topSearch
                ? '"' . mb_substr($topSearch->query, 0, 20) . '" (' . $topSearch->search_count . ')'
                : 'لا يوجد')
                ->description($topSearch ? match($topSearch->search_type) {
                    'books' => '📚 بحث في الكتب',
                    'authors' => '👤 بحث في المؤلفين',
                    'content' => '📄 بحث في المحتوى',
                    default => '',
                } : '')
                ->color('warning'),

            Stat::make('توزيع الأنواع', implode(' | ', [
                    '📚' . $distribution['books'],
                    '👤' . $distribution['authors'],
                    '📄' . $distribution['content'],
                ]))
                ->description('كتب | مؤلفين | محتوى')
                ->color('info'),
        ];
    }
}
