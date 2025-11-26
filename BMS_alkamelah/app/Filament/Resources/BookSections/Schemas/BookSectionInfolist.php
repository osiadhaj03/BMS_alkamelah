<?php

namespace App\Filament\Resources\BookSections\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookSectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('parent.name')
                    ->label('Parent')
                    ->placeholder('-'),
                TextEntry::make('sort_order')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('slug')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('logo_path')
                    ->placeholder('-'),
                TextEntry::make('icon_type')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('icon_url')
                    ->placeholder('-'),
                TextEntry::make('icon_name')
                    ->placeholder('-'),
                TextEntry::make('icon_color')
                    ->placeholder('-'),
                TextEntry::make('icon_size')
                    ->badge(),
                TextEntry::make('icon_custom_size')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('icon_library')
                    ->badge()
                    ->placeholder('-'),
            ]);
    }
}
