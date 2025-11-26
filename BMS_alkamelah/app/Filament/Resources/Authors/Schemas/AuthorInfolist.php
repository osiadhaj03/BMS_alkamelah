<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuthorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('full_name'),
                TextEntry::make('slug')
                    ->placeholder('-'),
                TextEntry::make('biography')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('madhhab')
                    ->badge()
                    ->placeholder('-'),
                IconEntry::make('is_living')
                    ->boolean(),
                TextEntry::make('birth_year_type')
                    ->badge(),
                TextEntry::make('birth_year')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('death_year_type')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('death_year')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('birth_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('death_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('author_role')
                    ->placeholder('-'),
            ]);
    }
}
