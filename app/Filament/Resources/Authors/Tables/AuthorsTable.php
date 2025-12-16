<?php

namespace App\Filament\Resources\Authors\Tables;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuthorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                
                TextColumn::make('first_name')
                    ->label('الاسم الأول')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('middle_name')
                    ->label('الاسم الأوسط')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('last_name')
                    ->label('الاسم الأخير')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('laqab')
                    ->label('اللقب')
                    ->searchable()
                    ->toggleable()
                    ->badge(),
                TextColumn::make('kunyah')
                    ->label('الكنية')
                    ->searchable()
                    ->toggleable()
                    ->badge(),
                TextColumn::make('madhhab')
                    ->label('المذهب')
                    ->toggleable()
                    ->badge(),
                IconColumn::make('is_living')
                    ->label('على قيد الحياة')
                    ->toggleable()
                    ->boolean(),
                TextColumn::make('birth_date')
                    ->label('تاريخ الولادة')
                    ->toggleable()
                    ->date()
                    ->sortable(),
                TextColumn::make('death_date')
                    ->label('تاريخ الوفاة')
                    ->toggleable()
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('madhhab')
                    ->label('المذهب')
                    ->options([
                        'المذهب الحنفي' => 'المذهب الحنفي',
                        'المذهب المالكي' => 'المذهب المالكي',
                        'المذهب الشافعي' => 'المذهب الشافعي',
                        'المذهب الحنبلي' => 'المذهب الحنبلي',
                        'آخرون' => 'آخرون',
                    ])
                    ->default('المذهب الحنفي')
                    ->native(false),

                SelectFilter::make('is_living')
                    ->label('على قيد الحياة')
                    ->options([
                        1 => 'نعم',
                        0 => 'لا',
                    ])
                    ->default(0)
                    ->native(false),

                Filter::make('birth_date')
                    ->label('تاريخ الولادة')
                    ->form([
                        DatePicker::make('birth_from')->label('من تاريخ'),
                        DatePicker::make('birth_until')->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['birth_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('birth_date', '>=', $date),
                            )
                            ->when(
                                $data['birth_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('birth_date', '<=', $date),
                            );
                    }),

                Filter::make('death_date')
                    ->label('تاريخ الوفاة')
                    ->form([
                        DatePicker::make('death_from')->label('من تاريخ'),
                        DatePicker::make('death_until')->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['death_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('death_date', '>=', $date),
                            )
                            ->when(
                                $data['death_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('death_date', '<=', $date),
                            );
                    }),
                    
            ])
            ->recordActions([
                //ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير')
                    ->color('success'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
                        ->label('تصدير المحدد'),
                ]),
            ]);
    }
}
