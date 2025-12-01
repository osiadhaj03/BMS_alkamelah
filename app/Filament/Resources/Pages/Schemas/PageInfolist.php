<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('book.title')
                    ->label('Book'),
                TextEntry::make('volume.title')
                    ->label('Volume')
                    ->placeholder('-'),
                TextEntry::make('chapter.title')
                    ->label('Chapter')
                    ->placeholder('-'),
                TextEntry::make('page_number')
                    ->numeric(),
                TextEntry::make('internal_index')
                    ->placeholder('-'),
                TextEntry::make('part')
                    ->placeholder('-'),
                TextEntry::make('content')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('original_page_number')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('html_content')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
