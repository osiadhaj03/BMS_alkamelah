<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class HourlyVisitsChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع الزيارات حسب الساعة (اليوم)';
    protected static ?int $sort = 23;

    protected function getData(): array
    {
        $hourly = PageVisit::humans()
            ->whereDate('visited_at', today())
            ->selectRaw('HOUR(visited_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $data = collect(range(0, 23))->map(fn ($h) => $hourly[$h] ?? 0)->toArray();
        $labels = collect(range(0, 23))->map(fn ($h) => sprintf('%02d:00', $h))->toArray();

        return [
            'datasets' => [[
                'label' => 'الزيارات',
                'data' => $data,
                'backgroundColor' => '#3b82f6',
                'borderRadius' => 4,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
