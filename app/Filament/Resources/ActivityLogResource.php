<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\KeyValue;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'سجل النشاطات';

    protected static UnitEnum|string|null $navigationGroup = 'النظام';
    
    protected static ?int $navigationSort = 100;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات')
                    ->schema([
                        KeyValue::make('properties.attributes')
                            ->label('البيانات الجديدة'),
                        KeyValue::make('properties.old')
                            ->label('البيانات القديمة'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التوقيت')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('المستخدم')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('event')
                    ->label('الحدث')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'created' => 'إضافة',
                        'updated' => 'تعديل',
                        'deleted' => 'حذف',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('النوع')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'App\Models\Book' => 'كتاب',
                            'App\Models\Author' => 'مؤلف',
                            'App\Models\User' => 'مستخدم',
                            default => class_basename($state),
                        };
                    }),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->searchable()
                    ->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
