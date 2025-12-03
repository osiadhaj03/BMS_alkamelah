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
                        
                        Textarea::make('description')
                            ->label('وصف الكتاب')
                            ->default(null)
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
                        
                        Toggle::make('has_original_pagination')
                            ->label('يحتوي على ترقيم الصفحات الأصلي'),
                    ])
                    ->columnSpanFull(),



                Section::make('البيانات الوصفية')
                    ->relationship('bookMetadata')
                    ->schema([
                        FileUpload::make('images')
                            ->label('الصور')
                            ->multiple()
                            ->image()
                            ->directory('book-images')
                            ->columnSpanFull(),

                        Repeater::make('video_links')
                            ->label('روابط الفيديو')
                            ->schema([
                                TextInput::make('title')->label('العنوان')->required(),
                                TextInput::make('url')->label('الرابط')->url()->required(),
                                TextInput::make('description')->label('الوصف'),
                            ])
                            ->columnSpanFull(),

                        TextInput::make('edition')
                            ->label('الطبعة'),

                        TextInput::make('edition_year')
                            ->label('سنة الطبعة')
                            ->numeric(),

                        Repeater::make('download_links')
                            ->label('روابط التحميل')
                            ->schema([
                                TextInput::make('title')->label('العنوان')->required(),
                                TextInput::make('url')->label('الرابط')->url()->required(),
                                TextInput::make('type')->label('النوع (PDF, etc)'),
                            ])
                            ->columnSpanFull(),

                        
                    ]) ->columnSpanFull(),
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
