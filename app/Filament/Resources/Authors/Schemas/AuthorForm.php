<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

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
                            ->placeholder('مثال: النعمان بن ثابت التيمي الكوفي')
                            ->columnSpanFull(),
                        
                        TextInput::make('famous_name')
                            ->label('الاسم المشهور')
                            ->maxLength(255)
                            ->placeholder('مثال: أبو حنيفة النعمان، ابن تيمية، الغزالي')
                            ->helperText('الاسم الذي اشتهر به المؤلف بين الناس')
                            ->columnSpanFull(),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('الاسم الأول')
                                    ->maxLength(255)
                                    ->placeholder('مثال: النعمان'),

                                TextInput::make('middle_name')
                                    ->label('الاسم الأوسط')
                                    ->maxLength(255)
                                    ->placeholder('مثال : بن ثابت'),

                                TextInput::make('last_name')
                                    ->label('الاسم الأخير')
                                    ->maxLength(255)
                                    ->placeholder('مثال: التيمي'),
                            ]),

                       

                        Grid::make(2)
                            ->schema([
                                TextInput::make('laqab')
                                    ->label('اللقب')
                                    ->maxLength(255)
                                    ->placeholder(' مثال: الإمام الأعظم، الإمام، الشيخ، العلامة ،الدكتور'),

                                TextInput::make('kunyah')
                                    ->label('الكنية')
                                    ->maxLength(255)
                                    ->placeholder('مثال: أبو حنيفة، أبو يوسف'),
                            ]),

                        FileUpload::make('image')
                            ->label('صورة المؤلف')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('authors')
                            ->saveUploadedFileUsing(function ($file, $record) {
                                return Storage::disk('public')->putFile('authors', $file);
                            })
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
                                    ->label('سنة الولادة (هجري)')
                                    ->numeric()
                                    ->placeholder('150')
                                    ->helperText('أدخل السنة الهجرية فقط'),

                                TextInput::make('death_date')
                                    ->label('سنة الوفاة (هجري)')
                                    ->numeric()
                                    ->placeholder('204')
                                    ->helperText('أدخل السنة الهجرية فقط')
                                    ->hidden(fn ($get) => $get('is_living') == 1),
                            ]),
                    ])
                     ->columnSpanFull(),

                Section::make('روابط الفيديو')
                    ->description('إضافة روابط فيديوهات عن المؤلف')
                    ->icon('heroicon-o-video-camera')
                    ->schema([
                        Repeater::make('video_links')
                            ->label('')
                            ->schema([
                                TextInput::make('url')
                                    ->label('رابط الفيديو')
                                    ->required()
                                    ->placeholder('https://youtube.com/watch?v=...')
                                    ->columnSpan(2),

                                TextInput::make('title')
                                    ->label('عنوان الفيديو')
                                    ->placeholder('مثال: محاضرة عن الإمام')
                                    ->columnSpan(2),

                                Textarea::make('description')
                                    ->label('وصف الفيديو')
                                    ->rows(2)
                                    ->placeholder('وصف مختصر للفيديو')
                                    ->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'فيديو جديد')
                            ->addActionLabel('إضافة فيديو')
                            ->reorderable()
                            ->columnSpanFull()
                            ->defaultItems(0),
                    ])
                    ->columnSpanFull()  
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
