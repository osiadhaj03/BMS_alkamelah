<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                ->label('عنوان الكتاب')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('shamela_id')
                ->label('رابط الكتاب من المكتبة الشاملة')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('visibility')
                ->label('الظهور')
                ->searchable()
                ->toggleable()
                    ->badge(),
                TextColumn::make('bookSection.name')
                ->label('القسم')
                ->searchable()
                ->toggleable(),

                TextColumn::make('publisher.name')
                ->label('الناشر')
                ->searchable()
                ->toggleable(),
                TextColumn::make('created_at')
                ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->toggleable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                ->label('تاريخ التحديث')
                ->searchable()
                ->toggleable()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('has_original_pagination')
                ->label('وفق المطبوع')
                ->toggleable()
                    ->boolean(),
                TextColumn::make('volumes_count')
                    ->label('عدد المجلدات')
                    ->getStateUsing(fn ($record) => $record->volumes()->count())
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('chapters_count')
                    ->label('عدد الفصول')
                    ->getStateUsing(fn ($record) => $record->chapters()->count())
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('pages_count')
                    ->label('عدد الصفحات')
                    ->getStateUsing(fn ($record) => $record->pages()->count())
                    ->toggleable()
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('visibility')
                    ->label('الظهور')
                    ->options([
                        '1' => 'مفعل',
                        '0' => 'غير مفعل',
                    ]),
                SelectFilter::make('has_original_pagination')
                    ->label('وفق المطبوع')
                    ->options([
                        '1' => 'مفعل',
                        '0' => 'غير مفعل',
                    ]),
                
                TernaryFilter::make('shamela_id')
                    ->label('مرتبط بالشاملة')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('shamela_id'),
                        false: fn (Builder $query) => $query->whereNull('shamela_id'),
                    ),

                TernaryFilter::make('pages')
                    ->label('يحتوي على صفحات')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('pages'),
                        false: fn (Builder $query) => $query->whereDoesntHave('pages'),
                    ),

                TernaryFilter::make('download_links')
                    ->label('يحتوي على روابط تحميل')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('bookMetadata', fn (Builder $q) => $q->whereNotNull('download_links')->where('download_links', '!=', '[]')),
                        false: fn (Builder $query) => $query->whereDoesntHave('bookMetadata', fn (Builder $q) => $q->whereNotNull('download_links')->where('download_links', '!=', '[]')),
                    ),

                TernaryFilter::make('video_links')
                    ->label('يحتوي على فيديو')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('bookMetadata', fn (Builder $q) => $q->whereNotNull('video_links')->where('video_links', '!=', '[]')),
                        false: fn (Builder $query) => $query->whereDoesntHave('bookMetadata', fn (Builder $q) => $q->whereNotNull('video_links')->where('video_links', '!=', '[]')),
                    ),

                TernaryFilter::make('images')
                    ->label('يحتوي على صور')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('bookMetadata', fn (Builder $q) => $q->whereNotNull('images')->where('images', '!=', '[]')),
                        false: fn (Builder $query) => $query->whereDoesntHave('bookMetadata', fn (Builder $q) => $q->whereNotNull('images')->where('images', '!=', '[]')),
                    ),

            ], layout: FiltersLayout::AboveContentCollapsible)
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
