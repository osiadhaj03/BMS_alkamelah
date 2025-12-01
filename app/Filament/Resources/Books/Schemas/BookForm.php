<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('shamela_id')
                    ->default(null),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('visibility')
                    ->options(['public' => 'Public', 'private' => 'Private', 'restricted' => 'Restricted'])
                    ->default('public')
                    ->required(),
                Select::make('book_section_id')
                    ->relationship('bookSection', 'name')
                    ->default(null),
                Select::make('publisher_id')
                    ->relationship('publisher', 'name')
                    ->default(null),
                Toggle::make('has_original_pagination'),
                Textarea::make('additional_notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
