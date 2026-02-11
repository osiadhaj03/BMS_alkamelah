<?php

namespace App\Filament\Resources\FeedbackComplaints\Widgets;

use App\Models\FeedbackComplaint;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FeedbackComplaintsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalFeedback = FeedbackComplaint::count();
        $pendingFeedback = FeedbackComplaint::where('status', 'pending')->count();
        $resolvedFeedback = FeedbackComplaint::where('status', 'resolved')->count();
        $urgentFeedback = FeedbackComplaint::whereIn('priority', ['urgent', 'high'])->count();

        return [
            Stat::make('إجمالي الرسائل', $totalFeedback)
                ->description('العدد الكلي للشكاوى والملاحظات')
                ->descriptionIcon('heroicon-o-envelope')
                ->color('success'),

            Stat::make('قيد الانتظار', $pendingFeedback)
                ->description('الرسائل التي تحتاج متابعة')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('تم الحل', $resolvedFeedback)
                ->description('الرسائل المحلولة')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('عاجلة ومهمة', $urgentFeedback)
                ->description('رسائل ذات أولوية عالية')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
