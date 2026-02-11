<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SearchLogResource\Pages;
use App\Models\SearchLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class SearchLogResource extends Resource
{
    protected static ?string $model = SearchLog::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = 'سجل البحث';

    protected static UnitEnum|string|null $navigationGroup = 'الإحصائيات';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('search_type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'books'   => 'info',
                        'authors' => 'warning',
                        'content' => 'success',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'books'   => '📚 كتب',
                        'authors' => '👤 مؤلفين',
                        'content' => '📄 محتوى',
                        default   => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('query')
                    ->label('نص البحث')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->query)
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('تم نسخ نص البحث!'),

                Tables\Columns\TextColumn::make('search_mode')
                    ->label('طريقة البحث')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'exact_match'    => 'danger',
                        'flexible_match' => 'info',
                        'morphological'  => 'purple',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'exact_match'    => 'مطابق',
                        'flexible_match' => 'مرن',
                        'morphological'  => 'صرفي',
                        default          => '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('results_count')
                    ->label('النتائج')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => $state === 0 ? 'danger' : 'success')
                    ->badge(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم النسخ!')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('word_order')
                    ->label('ترتيب الكلمات')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'consecutive'     => 'متتالية',
                        'same_paragraph'  => 'نفس الفقرة',
                        'any_order'       => 'أي ترتيب',
                        default           => '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('word_match')
                    ->label('شرط الكلمات')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'all_words'  => 'كل الكلمات',
                        'some_words' => 'بعض الكلمات',
                        default      => '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->created_at?->format('Y-m-d H:i')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('search_type')
                    ->label('نوع البحث')
                    ->options([
                        'books'   => '📚 كتب',
                        'authors' => '👤 مؤلفين',
                        'content' => '📄 محتوى',
                    ]),

                Tables\Filters\SelectFilter::make('search_mode')
                    ->label('طريقة البحث')
                    ->options([
                        'exact_match'    => 'مطابق',
                        'flexible_match' => 'مرن',
                        'morphological'  => 'صرفي',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('من تاريخ'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),

                Tables\Filters\Filter::make('zero_results')
                    ->label('بدون نتائج')
                    ->query(fn ($query) => $query->where('results_count', 0))
                    ->toggle(),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSearchLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
