<?php

namespace App\Filament\Resources\ActivityLogResource\Widgets;

use Spatie\Activitylog\Models\Activity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActivityLogStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalActivities = Activity::count();
        $todayActivities = Activity::whereDate('created_at', today())->count();
        $weekActivities = Activity::where('created_at', '>=', now()->subDays(7))->count();
        $monthActivities = Activity::where('created_at', '>=', now()->subDays(30))->count();

        return [
            Stat::make('إجمالي النشاطات', $totalActivities)
                ->description('العدد الكلي للنشاطات المسجلة')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('success'),

            Stat::make('نشاطات اليوم', $todayActivities)
                ->description('النشاطات المسجلة اليوم')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('آخر 7 أيام', $weekActivities)
                ->description('النشاطات في الأسبوع الماضي')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make('آخر 30 يوم', $monthActivities)
                ->description('النشاطات في الشهر الماضي')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),
        ];
    }
}
