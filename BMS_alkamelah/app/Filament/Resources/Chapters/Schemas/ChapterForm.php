<?php

namespace App\Filament\Resources\Chapters\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChapterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('volume_id')
                    ->relationship('volume', 'title')
                    ->default(null),
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'title')
                    ->default(null),
                TextInput::make('level')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('order')
                    ->numeric()
                    ->default(0),
                TextInput::make('page_start')
                    ->numeric()
                    ->default(null),
                TextInput::make('page_end')
                    ->numeric()
                    ->default(null),
                TextInput::make('estimated_reading_time')
                    ->numeric()
                    ->default(null),
                TextInput::make('internal_index_start')
                    ->numeric()
                    ->default(null),
                TextInput::make('internal_index_end')
                    ->numeric()
                    ->default(null),
                TextInput::make('start_page_internal_index')
                    ->numeric()
                    ->default(null),
                TextInput::make('end_page_internal_index')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
