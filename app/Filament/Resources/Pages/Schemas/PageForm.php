<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                Select::make('volume_id')
                    ->relationship('volume', 'title')
                    ->default(null),
                Select::make('chapter_id')
                    ->relationship('chapter', 'title')
                    ->default(null),
                TextInput::make('page_number')
                    ->required()
                    ->numeric(),
                TextInput::make('internal_index')
                    ->default(null),
                TextInput::make('part')
                    ->default(null),
                Textarea::make('content')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('original_page_number')
                    ->numeric()
                    ->default(null),
                Textarea::make('html_content')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
