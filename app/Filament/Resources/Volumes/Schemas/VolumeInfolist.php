<?php

namespace App\Filament\Resources\Volumes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VolumeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('book.title')
                    ->label('Book'),
                TextEntry::make('number')
                    ->numeric(),
                TextEntry::make('title')
                    ->placeholder('-'),
                TextEntry::make('page_start')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('page_end')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
