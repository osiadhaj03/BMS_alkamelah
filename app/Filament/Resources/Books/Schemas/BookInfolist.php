<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
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
                TextEntry::make('slug'),
                ImageEntry::make('cover_image')
                    ->placeholder('-'),
                TextEntry::make('pages_count')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('volumes_count')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('visibility')
                    ->badge(),
                TextEntry::make('source_url')
                    ->placeholder('-'),
                TextEntry::make('book_section_id')
                    ->numeric()
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
                TextEntry::make('edition_DATA')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('edition')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('edition_number')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('has_original_pagination')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('publication_year')
                    ->placeholder('-'),
                TextEntry::make('publication_place')
                    ->placeholder('-'),
                TextEntry::make('volume_count')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('page_count')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('isbn')
                    ->placeholder('-'),
                TextEntry::make('author_death_year')
                    ->placeholder('-'),
                TextEntry::make('author_role')
                    ->placeholder('-'),
                TextEntry::make('edition_info')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('additional_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ososa')
                    ->numeric()
                    ->placeholder('-'),
            ]);
    }
}
