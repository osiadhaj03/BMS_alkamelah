<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('shamela_id')
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('visibility')
                    ->badge(),
                TextEntry::make('bookSection.name')
                    ->label('Book section')
                    ->placeholder('-'),
                TextEntry::make('publisher.name')
                    ->label('Publisher')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('has_original_pagination')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('additional_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
