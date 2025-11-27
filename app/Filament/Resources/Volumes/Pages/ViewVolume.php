<?php

namespace App\Filament\Resources\Volumes\Pages;

use App\Filament\Resources\Volumes\VolumeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVolume extends ViewRecord
{
    protected static string $resource = VolumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
