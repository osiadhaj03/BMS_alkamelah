<?php

namespace App\Filament\Resources\NewsletterSubscribers\Widgets;

use App\Models\NewsletterSubscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsletterSubscribersStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSubscribers = NewsletterSubscriber::count();
        $todaySubscribers = NewsletterSubscriber::whereDate('created_at', today())->count();
        $weekSubscribers = NewsletterSubscriber::where('created_at', '>=', now()->subWeek())->count();
        $monthSubscribers = NewsletterSubscriber::where('created_at', '>=', now()->subMonth())->count();

        return [
            Stat::make('إجمالي المشتركين', $totalSubscribers)
                ->description('العدد الكلي للمشتركين')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Stat::make('المشتركون اليوم', $todaySubscribers)
                ->description('اشتراكات جديدة اليوم')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('primary'),

            Stat::make('آخر أسبوع', $weekSubscribers)
                ->description('اشتراكات آخر 7 أيام')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make('آخر شهر', $monthSubscribers)
                ->description('اشتراكات آخر 30 يوم')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),
        ];
    }
}
