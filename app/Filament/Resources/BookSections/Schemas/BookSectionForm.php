<?php

namespace App\Filament\Resources\BookSections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BookSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->default(null),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('slug')
                    ->default(null),
                TextInput::make('logo_path')
                    ->default(null),
                Select::make('icon_type')
                    ->options(['upload' => 'Upload', 'url' => 'Url', 'library' => 'Library', 'color' => 'Color'])
                    ->default(null),
                TextInput::make('icon_url')
                    ->url()
                    ->default(null),
                TextInput::make('icon_name')
                    ->default(null),
                TextInput::make('icon_color')
                    ->default(null),
                Select::make('icon_size')
                    ->options(['sm' => 'Sm', 'md' => 'Md', 'lg' => 'Lg', 'xl' => 'Xl'])
                    ->default('md')
                    ->required(),
                TextInput::make('icon_custom_size')
                    ->numeric()
                    ->default(null),
                Select::make('icon_library')
                    ->options(['heroicons' => 'Heroicons', 'fontawesome' => 'Fontawesome', 'custom' => 'Custom'])
                    ->default(null),
            ]);
    }
}
