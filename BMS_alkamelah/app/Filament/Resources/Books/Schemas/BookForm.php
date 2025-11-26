<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
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
                TextInput::make('slug')
                    ->required(),
                FileUpload::make('cover_image')
                    ->image(),
                TextInput::make('pages_count')
                    ->numeric()
                    ->default(null),
                TextInput::make('volumes_count')
                    ->numeric()
                    ->default(1),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'review' => 'Review', 'published' => 'Published', 'archived' => 'Archived'])
                    ->default('draft')
                    ->required(),
                Select::make('visibility')
                    ->options(['public' => 'Public', 'private' => 'Private', 'restricted' => 'Restricted'])
                    ->default('public')
                    ->required(),
                TextInput::make('source_url')
                    ->url()
                    ->default(null),
                TextInput::make('book_section_id')
                    ->numeric()
                    ->default(null),
                Select::make('publisher_id')
                    ->relationship('publisher', 'name')
                    ->default(null),
                TextInput::make('edition_DATA')
                    ->numeric()
                    ->default(null),
                TextInput::make('edition')
                    ->numeric()
                    ->default(null),
                TextInput::make('edition_number')
                    ->numeric()
                    ->default(null),
                Toggle::make('has_original_pagination'),
                TextInput::make('publication_year')
                    ->default(null),
                TextInput::make('publication_place')
                    ->default(null),
                TextInput::make('volume_count')
                    ->numeric()
                    ->default(1),
                TextInput::make('page_count')
                    ->numeric()
                    ->default(null),
                TextInput::make('isbn')
                    ->default(null),
                TextInput::make('author_death_year')
                    ->default(null),
                TextInput::make('author_role')
                    ->default('مؤلف'),
                Textarea::make('edition_info')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('additional_notes')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('ososa')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
