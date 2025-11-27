<?php

namespace App\Filament\Resources\BookSections\Pages;

use App\Filament\Resources\BookSections\BookSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBookSection extends EditRecord
{
    protected static string $resource = BookSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
