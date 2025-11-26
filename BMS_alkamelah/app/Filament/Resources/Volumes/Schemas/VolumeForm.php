<?php

namespace App\Filament\Resources\Volumes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VolumeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required(),
                TextInput::make('number')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->default(null),
                TextInput::make('page_start')
                    ->numeric()
                    ->default(null),
                TextInput::make('page_end')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
