<?php

namespace App\Filament\Widgets;

use App\Models\BookSection;
use Filament\Widgets\ChartWidget;

class BooksBySectionChart extends ChartWidget
{
    public ?string $heading = 'توزيع الكتب حسب الأقسام';

    public ?string $description = 'نسبة الكتب في كل قسم';

    protected function getData(): array
    {
        $sections = BookSection::withCount('books')->get();

        $labels = $sections->pluck('name')->toArray();
        $data = $sections->pluck('books_count')->toArray();

        $colors = [
            '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6',
            '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#84cc16',
        ];

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($labels)),
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
