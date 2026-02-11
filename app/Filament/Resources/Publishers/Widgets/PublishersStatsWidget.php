<?php

namespace App\Filament\Resources\Publishers\Widgets;

use App\Models\Publisher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PublishersStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPublishers = Publisher::count();
        $activePublishers = Publisher::where('is_active', true)->count();
        $recentPublishers = Publisher::where('created_at', '>=', now()->subMonth())->count();
        $inactivePublishers = Publisher::where('is_active', false)->count();

        return [
            Stat::make('إجمالي الناشرين', $totalPublishers)
                ->description('العدد الكلي للناشرين')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('success'),

            Stat::make('الناشرون النشطون', $activePublishers)
                ->description('الناشرون المفعلون')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('primary'),

            Stat::make('المضافون آخر شهر', $recentPublishers)
                ->description('ناشرون جدد في آخر 30 يوم')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('info'),

            Stat::make('غير النشطين', $inactivePublishers)
                ->description('الناشرون غير المفعلين')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
