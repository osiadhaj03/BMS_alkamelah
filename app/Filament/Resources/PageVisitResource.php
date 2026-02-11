<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageVisitResource\Pages;
use App\Models\PageVisit;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PageVisitResource extends Resource
{
    protected static ?string $model = PageVisit::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'زيارات الصفحات';

    protected static UnitEnum|string|null $navigationGroup = 'الإحصائيات';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_bot')
                    ->label('🤖')
                    ->boolean()
                    ->tooltip(fn ($record) => $record->is_bot
                        ? "Bot: {$record->bot_name}"
                        : 'زائر حقيقي'
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('عنوان IP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم النسخ!')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('page_title')
                    ->label('الصفحة')
                    ->searchable()
                    ->sortable()
                    ->default(fn ($record) => $record->route_name ?? '-')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'الصفحة الرئيسية' => 'success',
                        'البحث' => 'warning',
                        'تصفح الكتب' => 'info',
                        'قراءة كتاب' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->url)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('session_id')
                    ->label('الجلسة')
                    ->limit(12)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('مدة البقاء')
                    ->formatStateUsing(fn ($state) => $state
                        ? PageVisit::formatDuration($state)
                        : '-'
                    )
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state === null => 'gray',
                        $state < 10 => 'danger',
                        $state < 60 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('referer')
                    ->label('المصدر')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('visited_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->visited_at?->format('Y-m-d H:i')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_bot')
                    ->label('نوع الزائر')
                    ->options([
                        '0' => 'زوار حقيقيين',
                        '1' => 'Bots',
                    ]),

                Tables\Filters\SelectFilter::make('route_name')
                    ->label('الصفحة')
                    ->options([
                        'home' => 'الصفحة الرئيسية',
                        'search.index' => 'البحث',
                        'books.index' => 'الكتب',
                        'book.read' => 'قراءة كتاب',
                        'authors.index' => 'المؤلفين',
                        'author.show' => 'صفحة مؤلف',
                        'articles.index' => 'المقالات',
                        'news.index' => 'الأخبار',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('visited_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('من تاريخ'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('visited_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('visited_at', '<=', $data['until']));
                    }),
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
            ->defaultSort('visited_at', 'desc')
            ->poll('60s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageVisits::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
