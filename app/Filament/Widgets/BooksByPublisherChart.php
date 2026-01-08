<?php

namespace App\Filament\Widgets;

use App\Models\Publisher;
use Filament\Widgets\ChartWidget;

class BooksByPublisherChart extends ChartWidget
{
    public ?string $heading = 'توزيع الكتب حسب الناشرين';

    public ?string $description = 'أكثر 10 ناشرين عدداً من الكتب';

    protected function getData(): array
    {
        $publishers = Publisher::withCount('books')
            ->orderByDesc('books_count')
            ->take(10)
            ->get();

        $labels = $publishers->pluck('name')->toArray();
        $data = $publishers->pluck('books_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الكتب',
                    'data' => $data,
                    'backgroundColor' => [
                        '#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6',
                        '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#84cc16',
                    ],
                    'borderColor' => '#fff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
