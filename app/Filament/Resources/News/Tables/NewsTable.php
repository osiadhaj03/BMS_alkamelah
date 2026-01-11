<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('الصورة')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('images/default-news.png')),
                
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),
                
                TextColumn::make('category')
                    ->label('التصنيف')
                    ->badge()
                    ->colors([
                        'primary' => 'announcement',
                        'success' => 'update',
                        'warning' => 'event',
                        'secondary' => 'general',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'announcement' => 'إعلان',
                        'update' => 'تحديث',
                        'event' => 'فعالية',
                        'general' => 'عام',
                        default => $state
                    })
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'info' => 'scheduled',
                        'danger' => 'archived',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'scheduled' => 'مجدول',
                        'archived' => 'مؤرشف',
                        default => $state
                    })
                    ->sortable(),
                
                IconColumn::make('is_pinned')
                    ->label('مثبت')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-eye'),
                
                TextColumn::make('author.name')
                    ->label('الكاتب')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('التصنيف')
                    ->options([
                        'announcement' => 'إعلان',
                        'update' => 'تحديث',
                        'event' => 'فعالية',
                        'general' => 'عام',
                    ]),
                
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'scheduled' => 'مجدول',
                        'archived' => 'مؤرشف',
                    ]),
                
                SelectFilter::make('is_pinned')
                    ->label('مثبت')
                    ->options([
                        1 => 'نعم',
                        0 => 'لا',
                    ]),
                
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }
}
