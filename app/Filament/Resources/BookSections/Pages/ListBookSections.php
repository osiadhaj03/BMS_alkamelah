<?php

namespace App\Filament\Resources\BookSections\Pages;

use App\Filament\Resources\BookSections\BookSectionResource;
use App\Filament\Resources\BookSections\Widgets\BookSectionsStatsWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookSections extends ListRecords
{
    protected static string $resource = BookSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BookSectionsStatsWidget::class,
        ];
    }
}
