<?php

namespace App\Filament\Resources\FeedbackComplaints\Pages;

use App\Filament\Resources\FeedbackComplaints\FeedbackComplaintResource;
use App\Filament\Resources\FeedbackComplaints\Widgets\FeedbackComplaintsStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFeedbackComplaints extends ListRecords
{
    protected static string $resource = FeedbackComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FeedbackComplaintsStatsWidget::class,
        ];
    }
}
