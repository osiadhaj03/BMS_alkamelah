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
                Section::make('تفاصيل النشاط')
                    ->schema([
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                KeyValue::make('properties.old')
                                    ->label('البيانات القديمة')
                                    ->columnSpan(1),
                                KeyValue::make('properties.attributes')
                                    ->label('البيانات الجديدة')
                                    ->columnSpan(1),
                            ]),
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
                    ->sortable()
                    ->width('150px'),
                    
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
                    ->label('العنصر')
                    ->formatStateUsing(function ($record) {
                        $type = match ($record->subject_type) {
                            'App\Models\Book' => 'كتاب',
                            'App\Models\Author' => 'مؤلف',
                            'App\Models\User' => 'مستخدم',
                            default => class_basename($record->subject_type),
                        };
                        
                        $name = $record->subject?->title 
                            ?? $record->subject?->name 
                            ?? $record->subject?->full_name 
                            ?? $record->properties['attributes']['title'] 
                            ?? $record->properties['attributes']['name'] 
                            ?? $record->description;
                            
                        return $type . ': ' . \Illuminate\Support\Str::limit($name, 30);
                    })
                    ->description(fn ($record) => $record->subject_type),
                    
                Tables\Columns\TextColumn::make('changes')
                    ->label('التغييرات')
                    ->state(function ($record) {
                        if ($record->event === 'updated' && isset($record->properties['attributes'])) {
                            $changes = array_keys($record->properties['attributes']);
                            // Filter out ignored internal fields if any
                            $changes = array_filter($changes, fn($key) => !in_array($key, ['updated_at']));
                            return empty($changes) ? 'تحديث' : 'تعديل: ' . implode(', ', $changes);
                        }
                        return match ($record->event) {
                            'created' => 'تم الإنشاء',
                            'deleted' => 'تم الحذف',
                            default => $record->description,
                        };
                    })
                    ->wrap()
                    ->badge()
                    ->color('gray'),
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
