<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Schemas\Components\DateTimePicker;
use Filament\Schemas\Components\FileUpload;
use Filament\Schemas\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Components\TagsInput;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المحتوى الأساسي')->schema([
                    TextInput::make('title')
                        ->label('عنوان المقال')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }),
                    
                    TextInput::make('slug')
                        ->label('الرابط')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    
                    Textarea::make('excerpt')
                        ->label('المقتطف')
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText('ملخص مختصر للمقال')
                        ->columnSpanFull(),
                    
                    RichEditor::make('content')
                        ->label('المحتوى')
                        ->required()
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory('article-attachments'),
                ])->columns(2),

                Section::make('التصنيف والحالة')->schema([
                    Select::make('category')
                        ->label('التصنيف')
                        ->options([
                            'fiqh' => 'فقه',
                            'hadith' => 'حديث',
                            'history' => 'تاريخ',
                            'literature' => 'أدب',
                            'technology' => 'تقنية',
                            'general' => 'عام',
                        ])
                        ->required()
                        ->default('general'),
                    
                    Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'draft' => 'مسودة',
                            'published' => 'منشور',
                            'scheduled' => 'مجدول',
                            'archived' => 'مؤرشف',
                        ])
                        ->required()
                        ->default('draft'),
                    
                    DateTimePicker::make('published_at')
                        ->label('تاريخ النشر')
                        ->default(now()),
                    
                    Toggle::make('is_featured')
                        ->label('مقال مميز'),
                    
                    TextInput::make('priority')
                        ->label('الأولوية')
                        ->numeric()
                        ->default(0),
                    
                    TextInput::make('reading_time')
                        ->label('وقت القراءة (بالدقائق)')
                        ->numeric()
                        ->helperText('سيتم الحساب تلقائياً إذا تُرك فارغاً'),
                ])->columns(2),

                Section::make('المؤلف والعلاقات')->schema([
                    Select::make('author_id')
                        ->label('الكاتب (من المستخدمين)')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->preload()
                        ->default(fn () => auth()->id()),
                    
                    TextInput::make('author_name')
                        ->label('اسم المؤلف (خارجي)')
                        ->helperText('إذا كان المؤلف ليس من المستخدمين'),
                    
                    Select::make('related_book_id')
                        ->label('كتاب ذو صلة')
                        ->relationship('relatedBook', 'title')
                        ->searchable()
                        ->preload(),
                    
                    Select::make('related_author_id')
                        ->label('مؤلف ذو صلة')
                        ->relationship('relatedAuthor', 'name')
                        ->searchable()
                        ->preload(),
                ])->columns(2),

                Section::make('الوسائط')->schema([
                    FileUpload::make('cover_image')
                        ->label('صورة الغلاف')
                        ->image()
                        ->maxSize(2048)
                        ->directory('article-images')
                        ->imageEditor()
                        ->columnSpanFull(),
                ])->collapsible(),

                Section::make('الكلمات المفتاحية والـ SEO')->schema([
                    TagsInput::make('tags')
                        ->label('الكلمات المفتاحية (Tags)')
                        ->helperText('اضغط Enter بعد كل كلمة')
                        ->columnSpanFull(),
                    
                    TextInput::make('meta_title')
                        ->label('عنوان SEO')
                        ->maxLength(60),
                    
                    Textarea::make('meta_description')
                        ->label('وصف SEO')
                        ->rows(2)
                        ->maxLength(160),
                    
                    Textarea::make('meta_keywords')
                        ->label('كلمات SEO المفتاحية')
                        ->rows(2),
                ])->columns(1)->collapsed(),
            ]);
    }
}
