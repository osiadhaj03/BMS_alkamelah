<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class TopPagesChart extends ChartWidget
{
    protected static ?string $heading = 'أكثر الصفحات زيارة (Top 10)';
    protected static ?int $sort = 22;

    protected function getData(): array
    {
        $topPages = PageVisit::topPages(10);

        return [
            'datasets' => [[
                'label' => 'عدد الزيارات',
                'data' => $topPages->pluck('visits_count')->toArray(),
                'backgroundColor' => [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    '#06b6d4', '#ec4899', '#84cc16', '#f97316', '#6366f1',
                ],
            ]],
            'labels' => $topPages->map(fn ($p) => mb_substr($p->page_title ?? $p->route_name ?? 'غير معروف', 0, 25))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}
