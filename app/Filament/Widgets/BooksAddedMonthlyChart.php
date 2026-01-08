<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BooksAddedMonthlyChart extends ChartWidget
{
    protected static ?string $heading = 'عدد الكتب المضافة شهرياً';

    protected static ?string $description = 'إحصائيات إضافة الكتب خلال آخر 12 شهر';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // جلب بيانات آخر 12 شهر
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $count = Book::whereBetween('created_at', [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth(),
            ])->count();

            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد الكتب المضافة',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                    'pointRadius' => 5,
                    'pointBackgroundColor' => '#10b981',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
