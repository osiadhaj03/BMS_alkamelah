<?php

namespace App\Filament\Resources\Books\Widgets;

use App\Models\Book;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('عدد الكتب', Book::count())
                ->description('إجمالي الكتب في النظام')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make('الكتب المضافة آخر شهر', Book::where('created_at', '>=', now()->subMonth())->count())
                ->description('خلال 30 يوم الماضية')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('الكتب المراجعة', Book::where('is_reviewed', true)->count())
                ->description('كتب تمت مراجعتها')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('كتب معلوماتها ناقصة', Book::whereDoesntHave('authors')->orWhereNull('book_section_id')->count())
                ->description('بدون مؤلف أو قسم')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
