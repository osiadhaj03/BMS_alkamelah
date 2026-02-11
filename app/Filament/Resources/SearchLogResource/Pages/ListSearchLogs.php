<?php

namespace App\Filament\Resources\SearchLogResource\Pages;

use App\Filament\Resources\SearchLogResource;
use App\Filament\Widgets\SearchStatsWidget;
use Filament\Resources\Pages\ListRecords;

class ListSearchLogs extends ListRecords
{
    protected static string $resource = SearchLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SearchStatsWidget::class,
        ];
    }
}
