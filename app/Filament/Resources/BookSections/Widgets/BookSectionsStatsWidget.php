<?php

namespace App\Filament\Resources\BookSections\Widgets;

use App\Models\BookSection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookSectionsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSections = BookSection::count();
        $mainSections = BookSection::whereNull('parent_id')->count();
        $subSections = BookSection::whereNotNull('parent_id')->count();
        $activeSections = BookSection::where('is_active', true)->count();

        return [
            Stat::make('إجمالي الأقسام', $totalSections)
                ->description('العدد الكلي لأقسام الكتب')
                ->descriptionIcon('heroicon-o-folder')
                ->color('success'),

            Stat::make('الأقسام الرئيسية', $mainSections)
                ->description('الأقسام الأساسية')
                ->descriptionIcon('heroicon-o-folder-open')
                ->color('primary'),

            Stat::make('الأقسام الفرعية', $subSections)
                ->description('الأقسام التابعة')
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('info'),

            Stat::make('الأقسام النشطة', $activeSections)
                ->description('الأقسام المفعلة حالياً')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('warning'),
        ];
    }
}
