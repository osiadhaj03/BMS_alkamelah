<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VisitsLineChart extends ChartWidget
{
    protected static ?string $heading = 'الزيارات اليومية (آخر 30 يوم)';
    protected static ?int $sort = 20;

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $visits = PageVisit::humans()
            ->where('visited_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(visited_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'الزيارات',
                    'data' => $days->map(fn ($d) => $visits[$d->format('Y-m-d')] ?? 0)->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $days->map(fn ($d) => $d->format('m/d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
