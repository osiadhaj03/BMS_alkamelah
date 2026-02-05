<?php

namespace App\Filament\Resources\BookSections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class BookSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات القسم')
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم القسم')
                            ->required()
                            ->columnSpanFull(),
                        
                        Grid::make(3)
                            ->schema([
                                Select::make('parent_id')
                                    ->label('القسم الرئيسي')
                                    ->relationship('parent', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(null),

                                TextInput::make('sort_order')
                                    ->label('الترتيب')
                                    ->required()
                                    ->numeric()
                                    ->default(0),

                                Select::make('is_active')
                                    ->label('نشط')
                                    ->required()
                                    ->options([
                                        1 => 'نعم',
                                        0 => 'لا',
                                    ])
                                    ->default(true),
                            ]),    

                        Textarea::make('description')
                            ->label('الوصف')
                            ->default(null)
                            ->columnSpanFull(),

                        FileUpload::make('logo_path')
                            ->label('أيقونة القسم')
                            ->image()
                            ->disk('public')
                            ->directory('book-sections-logos')
                            ->saveUploadedFileUsing(function ($file, $record) {
                                return Storage::disk('public')->putFile('book-sections-logos', $file);
                            })
                            ->columnSpanFull(),

                        
                    ])->columnSpanFull(),
            ]);
    }
}
