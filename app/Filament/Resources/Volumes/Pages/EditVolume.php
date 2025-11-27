<?php

namespace App\Filament\Resources\Volumes\Pages;

use App\Filament\Resources\Volumes\VolumeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVolume extends EditRecord
{
    protected static string $resource = VolumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
