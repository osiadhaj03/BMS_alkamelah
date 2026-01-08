<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\BookSection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الكتب', Book::count())
                ->description('عدد الكتب المسجلة في النظام')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success')
                ->icon('heroicon-o-book-open'),

            Stat::make('عدد المؤلفين', Author::count())
                ->description('عدد المؤلفين المسجلين')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->icon('heroicon-o-user-group'),

            Stat::make('عدد الناشرين', Publisher::count())
                ->description('عدد دور النشر المسجلة')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('warning')
                ->icon('heroicon-o-building-library'),

            Stat::make('عدد الأقسام', BookSection::count())
                ->description('عدد أقسام الكتب')
                ->descriptionIcon('heroicon-m-tag')
                ->color('danger')
                ->icon('heroicon-o-tag'),
        ];
    }
}
