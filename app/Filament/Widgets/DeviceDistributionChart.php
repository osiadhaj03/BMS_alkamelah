<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class DeviceDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع الأجهزة';
    protected static ?int $sort = 21;

    protected function getData(): array
    {
        $total = PageVisit::humans()->count() ?: 1;

        // استخدام عمود device_type إذا كانت البيانات متوفرة
        $hasDeviceData = PageVisit::humans()->whereNotNull('device_type')->exists();

        if ($hasDeviceData) {
            $counts = PageVisit::humans()
                ->selectRaw("device_type, COUNT(*) as count")
                ->groupBy('device_type')
                ->pluck('count', 'device_type');

            $desktop = $counts->get('desktop', 0);
            $mobile = $counts->get('mobile', 0);
            $tablet = $counts->get('tablet', 0);
        } else {
            // Fallback: تحليل من user_agent
            $mobile = PageVisit::humans()
                ->where(function ($q) {
                    $q->where('user_agent', 'like', '%Mobile%')
                      ->orWhere('user_agent', 'like', '%Android%')
                      ->orWhere('user_agent', 'like', '%iPhone%');
                })->count();
            $tablet = PageVisit::humans()
                ->where(function ($q) {
                    $q->where('user_agent', 'like', '%iPad%')
                      ->orWhere('user_agent', 'like', '%Tablet%');
                })->count();
            $desktop = $total - $mobile - $tablet;
        }

        return [
            'datasets' => [[
                'data' => [$desktop, $mobile, $tablet],
                'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b'],
            ]],
            'labels' => ['🖥️ Desktop', '📱 Mobile', '📟 Tablet'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
