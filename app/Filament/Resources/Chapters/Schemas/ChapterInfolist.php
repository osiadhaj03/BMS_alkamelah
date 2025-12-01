<?php

namespace App\Filament\Resources\Chapters\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChapterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('volume.title')
                    ->label('Volume')
                    ->placeholder('-'),
                TextEntry::make('book.title')
                    ->label('Book'),
                TextEntry::make('title'),
                TextEntry::make('parent.title')
                    ->label('Parent')
                    ->placeholder('-'),
                TextEntry::make('level')
                    ->numeric(),
                TextEntry::make('order')
                    ->numeric()
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
