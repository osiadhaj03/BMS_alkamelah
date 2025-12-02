<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الأساسية')
                    ->description('بيانات المؤلف الرئيسية')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('أدخل اسم المؤلف كاملاً'),

                        FileUpload::make('image')
                            ->label('صورة المؤلف')
                            ->image()
                            ->imageEditor()
                            ->directory('authors')
                            ->columnSpanFull(),

                        RichEditor::make('biography')
                            ->label('السيرة الذاتية')
                            ->placeholder('اكتب السيرة الذاتية للمؤلف...')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                                'source-ai',
                                'source-ai-transform',
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 300px;'])
                            ->columnSpanFull(),
                    ])
                     ->columnSpanFull(),

                Section::make('التفاصيل')
                    ->description('معلومات إضافية عن المؤلف')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Select::make('madhhab')
                            ->label('المذهب')
                            ->options([
                                'المذهب الحنفي' => 'المذهب الحنفي',
                                'المذهب المالكي' => 'المذهب المالكي',
                                'المذهب الشافعي' => 'المذهب الشافعي',
                                'المذهب الحنبلي' => 'المذهب الحنبلي',
                                'آخرون' => 'آخرون',
                            ])
                            ->placeholder('اختر المذهب')
                            ->searchable(),

                        Select::make('is_living')
                            ->label('هل على قيد الحياة؟')
                            ->options([
                                1 => 'نعم',
                                0 => 'لا',
                            ])
                            ->default(0)
                            ->required()
                            ->native(false)
                            ->live(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('birth_date')
                                    ->label('تاريخ الولادة')
                                    ->placeholder('ادخل التاريخ الهجري')
                                    ->helperText('مثال: 150 هـ'),

                                TextInput::make('death_date')
                                    ->label('تاريخ الوفاة')
                                    ->placeholder('ادخل التاريخ الهجري')
                                    ->helperText('مثال: 204 هـ')
                                    ->hidden(fn ($get) => $get('is_living') == 1),
                            ]),
                    ])
                     ->columnSpanFull(),
            ]);
    }
}
