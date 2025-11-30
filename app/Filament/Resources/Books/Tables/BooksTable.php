<?php

namespace App\Filament\Resources\Books\Tables;

use App\Models\Author;
use App\Models\Book;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                
                TextColumn::make('mainAuthors')
                    ->label('المؤلف الرئيسي')
                    ->getStateUsing(function ($record) {
                        $mainAuthor = $record->authorBooks
                            ->where('is_main', true)
                            ->first();
                        
                        if ($mainAuthor && $mainAuthor->author) {
                            return $mainAuthor->author->full_name;
                        }
                        
                        $firstAuthor = $record->authorBooks->first();
                        return $firstAuthor?->author?->full_name ?? 'غير محدد';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('authorBooks.author', function (Builder $query) use ($search) {
                            $query->where('full_name', 'like', "%{$search}%");
                        });
                    })
                    ->limit(30)
                    ->toggleable(),
                
                TextColumn::make('bookSection.name')
                    ->label('القسم')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable()
                    ->color('info'),
                
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'archived' => 'مؤرشف',
                        default => $state,
                    })
                    ->toggleable(),
                
                TextColumn::make('publisher.name')
                    ->label('الناشر')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                
                TextColumn::make('edition')
                    ->label('الطبعة')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (?int $state): string => $state ? "الطبعة {$state}" : 'غير محدد')
                    ->toggleable(),
                
                TextColumn::make('edition_DATA')
                    ->label('سنة الطباعة')
                    ->sortable()
                    ->badge()
                    ->color('secondary')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state}" : 'غير محدد')
                    ->toggleable(),
                
                TextColumn::make('volumes_count')
                    ->label('المجلدات')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(fn ($record) => $record->volumes_count ?? 0)
                    ->toggleable(),
                
                TextColumn::make('pages_count')
                    ->label('الصفحات')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn ($record) => $record->pages_count ?? 0)
                    ->toggleable(),
                
                TextColumn::make('visibility')
                    ->label('الرؤية')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'private' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => 'عام',
                        'private' => 'خاص',
                        default => $state,
                    })
                    ->toggleable(),
                
                TextColumn::make('source_url')
                    ->label('رابط المصدر')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'متوفر' : 'غير متوفر')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'danger')
                    ->url(fn ($record) => $record->source_url)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(100)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 100) {
                            return null;
                        }
                        return $state;
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('slug')
                    ->label('الرابط الثابت')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('book_section_id')
                    ->label('قسم الكتاب')
                    ->relationship('bookSection', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('جميع الأقسام'),

                SelectFilter::make('publisher_id')
                    ->label('الناشر')
                    ->relationship('publisher', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('جميع الناشرين'),

                SelectFilter::make('author')
                    ->label('المؤلف')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas(
                                'authorBooks.author',
                                fn (Builder $query): Builder => $query->where('id', $value)
                            )
                        );
                    })
                    ->options(function (): array {
                        return Author::whereHas('books')
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('جميع المؤلفين'),

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'archived' => 'مؤرشف',
                    ])
                    ->placeholder('جميع الحالات'),

                SelectFilter::make('visibility')
                    ->label('الرؤية')
                    ->options([
                        'public' => 'عام',
                        'private' => 'خاص',
                    ])
                    ->placeholder('جميع أنواع الرؤية'),

                TernaryFilter::make('has_source_url')
                    ->label('رابط المصدر')
                    ->placeholder('الكل')
                    ->trueLabel('مع رابط مصدر')
                    ->falseLabel('بدون رابط مصدر')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('source_url')->where('source_url', '!=', ''),
                        false: fn (Builder $query) => $query->where(function ($query) {
                            $query->whereNull('source_url')->orWhere('source_url', '');
                        }),
                        blank: fn (Builder $query) => $query,
                    ),

                TernaryFilter::make('has_pages')
                    ->label('وجود صفحات')
                    ->placeholder('الكل')
                    ->trueLabel('لديه صفحات')
                    ->falseLabel('بدون صفحات')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('pages'),
                        false: fn (Builder $query) => $query->whereDoesntHave('pages'),
                        blank: fn (Builder $query) => $query,
                    ),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                ViewAction::make()
                    ->label('عرض')
                    ->color('info'),
                EditAction::make()
                    ->label('تعديل')
                    ->color('warning'),
                DeleteAction::make()
                    ->label('حذف')
                    ->color('danger'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                    
                    BulkAction::make('publish')
                        ->label('نشر المحدد')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'published']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('archive')
                        ->label('أرشفة المحدد')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'archived']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('assign_to_section')
                        ->label('تعيين إلى قسم')
                        ->icon('heroicon-o-folder')
                        ->color('info')
                        ->form([
                            Select::make('book_section_id')
                                ->label('اختر القسم الجديد')
                                ->relationship('bookSection', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->placeholder('اختر القسم المطلوب')
                                ->helperText('سيتم تعيين جميع الكتب المحددة إلى هذا القسم'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['book_section_id' => $data['book_section_id']]);
                            });
                            
                            Notification::make()
                                ->title('تم تعيين الكتب بنجاح')
                                ->body('تم تعيين ' . $records->count() . ' كتاب إلى القسم الجديد')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('تعيين الكتب إلى قسم جديد')
                        ->modalDescription('هل أنت متأكد من تعيين الكتب المحددة إلى القسم الجديد؟')
                        ->modalSubmitActionLabel('تعيين الكتب')
                        ->modalCancelActionLabel('إلغاء'),
                ])
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s')
            ->deferLoading()
            ->toggleColumnsTriggerAction(
                fn (\Filament\Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('إظهار/إخفاء الأعمدة')
                    ->icon('heroicon-o-view-columns')
                    ->color('gray')
            );
    }
}
