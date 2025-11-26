<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shamela_id')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                ImageColumn::make('cover_image'),
                TextColumn::make('pages_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('volumes_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('visibility')
                    ->badge(),
                TextColumn::make('source_url')
                    ->searchable(),
                TextColumn::make('book_section_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('publisher.name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('edition_DATA')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('edition')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('edition_number')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('has_original_pagination')
                    ->boolean(),
                TextColumn::make('publication_year'),
                TextColumn::make('publication_place')
                    ->searchable(),
                TextColumn::make('volume_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('page_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('isbn')
                    ->searchable(),
                TextColumn::make('author_death_year'),
                TextColumn::make('author_role')
                    ->searchable(),
                TextColumn::make('ososa')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
