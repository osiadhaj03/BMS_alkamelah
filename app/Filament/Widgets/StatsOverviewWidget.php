<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\BookSection;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected int|array|null $columns = 4;
    
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

            Stat::make('الكتب المراجعة', Book::where('is_reviewed', true)->count())->description('عدد الكتب التي تم مراجعتها')->descriptionIcon('heroicon-m-check-circle')->color('success')->icon('heroicon-o-check-badge'),

            Stat::make('المؤلفين مع السيرة', Author::whereNotNull('biography')->where('biography', '!=', '')->count())->description('عدد المؤلفين الذين لديهم سيرة ذاتية')->descriptionIcon('heroicon-m-document-text')->color('info')->icon('heroicon-o-user-circle'),

            Stat::make('الناشرين - معلومات كاملة', Publisher::whereNotNull('name')->whereNotNull('country')->whereNotNull('address')->where('name', '!=', '')->count())->description('ناشرين لديهم معلومات كاملة')->descriptionIcon('heroicon-m-building-storefront')->color('warning')->icon('heroicon-o-building-library'),

            Stat::make('إجمالي المستخدمين', User::count())->description('عدد المستخدمين العاملين في المكتبة')->descriptionIcon('heroicon-m-users')->color('primary')->icon('heroicon-o-users'),
        ];
    }
}
