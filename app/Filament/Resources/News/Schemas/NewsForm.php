<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المحتوى الأساسي')->schema([
                    TextInput::make('title')
                        ->label('العنوان')
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
                        ->columnSpanFull(),
                    
                    RichEditor::make('content')
                        ->label('المحتوى')
                        ->required()
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory('news-attachments'),
                ])->columns(2),

                Section::make('التصنيف والحالة')->schema([
                    Select::make('category')
                        ->label('التصنيف')
                        ->options([
                            'announcement' => 'إعلان',
                            'update' => 'تحديث',
                            'event' => 'فعالية',
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
                    
                    Toggle::make('is_pinned')
                        ->label('خبر مثبت'),
                    
                    TextInput::make('priority')
                        ->label('الأولوية')
                        ->numeric()
                        ->default(0)
                        ->helperText('الأرقام الأعلى تظهر أولاً'),
                    
                    Select::make('author_id')
                        ->label('الكاتب')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->preload()
                        ->default(fn () => auth()->id()),
                ])->columns(2),

                Section::make('الوسائط')->schema([
                    FileUpload::make('featured_image')
                        ->label('الصورة المميزة')
                        ->image()
                        ->maxSize(2048)
                        ->directory('news-images')
                        ->imageEditor()
                        ->columnSpanFull(),
                ])->collapsible(),

                Section::make('تحسين محركات البحث (SEO)')->schema([
                    TextInput::make('meta_title')
                        ->label('عنوان SEO')
                        ->maxLength(60)
                        ->helperText('يُفضل أن يكون بين 50-60 حرف'),
                    
                    Textarea::make('meta_description')
                        ->label('وصف SEO')
                        ->rows(2)
                        ->maxLength(160)
                        ->helperText('يُفضل أن يكون بين 150-160 حرف'),
                    
                    Textarea::make('meta_keywords')
                        ->label('الكلمات المفتاحية')
                        ->rows(2)
                        ->helperText('افصل بين الكلمات بفاصلة'),
                ])->columns(1)->collapsed(),
            ]);
    }
}
