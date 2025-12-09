<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\RichEditor;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الكتاب الأساسية')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان الكتاب')
                            ->required()
                            ->columnSpanFull(),
                        


                        RichEditor::make('description')
                            ->label(' وصف الكتاب')
                            ->placeholder('اكتب وصف الكتاب...')
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

                        TextInput::make('shamela_id')
                            ->label('رابط الكتاب من المكتبة الشاملة')
                            ->default(null),

                        Select::make('visibility')
                            ->label('الظهور')
                            ->options([
                                'public' => 'عام',
                                'private' => 'خاص',
                                'restricted' => 'مقيّد'
                            ])
                            ->default('public')
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('معلومات الطبعة')
                    ->schema([
                        Select::make('has_original_pagination')
                            ->label('وفق المطبوع')
                            ->options([
                                'yes' => 'نعم',
                                'no' => 'لا',
                            ])
                            ->default('yes')
                            ->required(),
                        TextInput::make('edition')
                            ->label('رقم الطبعة')
                            ->placeholder('الطبعة 1 ، 2، ...')
                            ->numeric(),


                        TextInput::make('edition_year')
                            ->label('سنة الطبعة')
                            ->placeholder('سنة الطبعة(مثال: 1999)')
                            ->numeric(),    
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('التصنيف والمؤلفين والنشر')
                    ->schema([
                        Select::make('book_section_id')
                            ->label('القسم')
                            ->relationship('bookSection', 'name')
                            ->searchable()
                            ->preload()
                            ->default(null),

                        Repeater::make('authorBooks')
                            ->label('المؤلفين')
                            ->relationship()
                            ->schema([
                                Select::make('author_id')
                                    ->label('المؤلف')
                                    ->relationship('author', 'first_name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                
                                Select::make('role')
                                    ->label('الدور')
                                    ->options([
                                        'author' => 'مؤلف',
                                        'co_author' => 'مؤلف مشارك',
                                        'editor' => 'محقق',
                                        'translator' => 'مترجم',
                                        'reviewer' => 'مراجع',
                                        'commentator' => 'معلق',
                                    ])
                                    ->default('author')
                                    ->required(),
                                
                                Select::make('is_main')
                                    ->label('مؤلف رئيسي')
                                    ->options([
                                        true => 'نعم',
                                        false => 'لا',
                                    ])
                                    ->default(false)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->defaultItems(1)
                            ->addActionLabel('إضافة مؤلف'),

                        Select::make('publisher_id')
                            ->label('الناشر')
                            ->relationship('publisher', 'name')
                            ->searchable()
                            ->preload()
                            ->default(null),
                        
                       
                    ])
                    ->columnSpanFull(),



                Section::make('البيانات الوصفية')
                    ->relationship('bookMetadata')
                    ->schema([
                        FileUpload::make('images')
                            ->label('صور الكتاب')
                            ->multiple()
                            ->image()
                            ->maxFiles(3)
                            ->directory('book-images')
                            ->columnSpanFull(),

                        Repeater::make('video_links')
                            ->label('إضافة رابط فيديو أو أكثر عن الكتاب')
                            ->schema([
                                TextInput::make('url')
                                    ->label('رابط الفيديو')
                                    ->required()
                                    ->placeholder('https://youtube.com/watch?v=...')
                                    ->columnSpan(2),

                                TextInput::make('title')
                                    ->label('عنوان الفيديو')
                                    ->placeholder('مثال: شرح الكتاب')
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



                        Repeater::make('download_links')
                            ->label('روابط التحميل')
                            ->schema([
                                TextInput::make('url')
                                ->label('الرابط')
                                ->url()
                                ->required(),
                                Select::make('platform')
                                ->label('منصة التحميل')
                                ->options([
                                    'google_drive' => 'Google Drive',
                                    'dropbox' => 'Dropbox',
                                    'one_drive' => 'One Drive',
                                    'telegram' => 'Telegram',
                                    'alkamelah' => 'موقع المكتبةالكاملة',
                                    'Mega' => 'Mega',
                                    'archive_org' => 'Archive.org',
                                    'other' => 'آخر',
                                ])
                                ->required(),
                                Select::make('type')
                                ->label('صيغة الملف')
                                ->options([
                                    'pdf' => 'PDF',
                                    'word' => 'Word',
                                    'web' => 'WEB(Html)',
                                    'other' => 'آخر',
                                ]),
                                TextInput::make('notes')
                                    ->label('ملاحظات إضافية')
                                    ->placeholder('مثال: نسخة عالية الجودة، نسخة مصورة، ...')
                                    ->columnSpanFull()
                                    ->default(null),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'فيديو جديد')
                            ->addActionLabel('إضافة رابط تحميل')
                            ->reorderable()
                            ->columnSpanFull()
                            ->defaultItems(0),

                        
                    ])->collapsed() ->columnSpanFull(),
                Section::make('معلومات إضافية')
                    ->schema([
                        Textarea::make('additional_notes')
                            ->label('ملاحظات إضافية')
                            ->default(null)
                            ->columnSpanFull(),
                        KeyValue::make('metadata')
                            ->label('بيانات إضافية')
                            ->keyLabel('المفتاح')
                            ->valueLabel('القيمة')
                            ->columnSpanFull(),    
                    ])
                    ->collapsed() ->columnSpanFull(),
            ]);
    }
}
