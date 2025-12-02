<?php

namespace App\Filament\Resources\Authors\Tables;

use AlperenErsoy\Filament\Export\Actions\FilamentExportBulkAction;
use AlperenErsoy\Filament\Export\Actions\FilamentExportHeaderAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuthorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                
                TextColumn::make('first_name')
                    ->label('الاسم الأول')
                    ->toggleable(),
                TextColumn::make('middle_name')
                    ->label('الاسم الأوسط')
                    ->toggleable(),
                TextColumn::make('last_name')
                    ->label('الاسم الأخير')
                    ->toggleable(),
                TextColumn::make('laqab')
                    ->label('اللقب')
                    ->toggleable()
                    ->badge(),
                TextColumn::make('kunyah')
                    ->label('الكنية')
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
                //
            
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
                    ->native(false)
//->live(),
                SelectFilter::make('is_living')
                    ->label('على قيد الحياة')
                    ->options([
                        1 => 'نعم',
                        0 => 'لا',
                    ])
                    ->default(0)
                    ->native(false)
                    //->live(),
                SelectFilter::make('birth_date')
                    ->label('تاريخ الولادة')
                    ->date()
                    ->native(false)
                    //->live(),
                SelectFilter::make('death_date')
                    ->label('تاريخ الوفاة')
                    ->date()
                    ->native(false)
                 //   ->live(),
                    
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
