<?php

namespace App\Filament\Resources\Publishers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Storage;

class PublisherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الأساسية')
                    ->description('بيانات الناشر الرئيسية')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('اسم الناشر')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('اسم دار النشر'),

                                TextInput::make('website_url')
                                    ->label('الموقع الإلكتروني')
                                    ->url()
                                    ->placeholder('https://example.com')
                                    ->default(null),
                                Select::make('is_active')
                                    ->label('نشط')
                                    ->options([
                                        true => 'نعم',
                                        false => 'لا',
                                    ])
                                    ->default(true)
                                    ->required(),    
                            ]),

                        FileUpload::make('image')
                            ->label('شعار الناشر')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('publishers')
                            ->saveUploadedFileUsing(function ($file, $record) {
                                return Storage::disk('public')->putFile('publishers', $file);
                            })
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('نبذة عن الناشر')
                            ->placeholder('اكتب نبذة تعريفية عن دار النشر...')
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
                            ])
                            ->columnSpanFull(),
                        
                        
                    ])->columnSpanFull(),

                Section::make('معلومات الاتصال')
                    ->description('العنوان وطرق التواصل')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->placeholder('email@example.com')
                                    ->default(null),

                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->placeholder('رقم الهاتف للتواصل')
                                    ->default(null),
                                Select::make('country')
                                    ->label('الدولة')
                                    ->searchable()
                                    ->options([
                                        'المملكة العربية السعودية' => 'المملكة العربية السعودية',
                                        'مصر' => 'مصر',
                                        'الإمارات العربية المتحدة' => 'الإمارات العربية المتحدة',
                                        'الكويت' => 'الكويت',
                                        'قطر' => 'قطر',
                                        'البحرين' => 'البحرين',
                                        'عمان' => 'عمان',
                                        'الأردن' => 'الأردن',
                                        'لبنان' => 'لبنان',
                                        'سوريا' => 'سوريا',
                                        'العراق' => 'العراق',
                                        'اليمن' => 'اليمن',
                                        'فلسطين' => 'فلسطين',
                                        'المغرب' => 'المغرب',
                                        'الجزائر' => 'الجزائر',
                                        'تونس' => 'تونس',
                                        'ليبيا' => 'ليبيا',
                                        'السودان' => 'السودان',
                                        'موريتانيا' => 'موريتانيا',
                                        'الصومال' => 'الصومال',
                                        'جيبوتي' => 'جيبوتي',
                                        'جزر القمر' => 'جزر القمر',
                                        'تركيا' => 'تركيا',
                                        'إيران' => 'إيران',
                                        'باكستان' => 'باكستان',
                                        'أفغانستان' => 'أفغانستان',
                                        'إندونيسيا' => 'إندونيسيا',
                                        'ماليزيا' => 'ماليزيا',
                                        'الولايات المتحدة الأمريكية' => 'الولايات المتحدة الأمريكية',
                                        'المملكة المتحدة' => 'المملكة المتحدة',
                                        'فرنسا' => 'فرنسا',
                                        'ألمانيا' => 'ألمانيا',
                                        'إيطاليا' => 'إيطاليا',
                                        'إسبانيا' => 'إسبانيا',
                                        'روسيا' => 'روسيا',
                                        'الصين' => 'الصين',
                                        'الهند' => 'الهند',
                                        'اليابان' => 'اليابان',
                                        'كوريا الجنوبية' => 'كوريا الجنوبية',
                                        'أستراليا' => 'أستراليا',
                                        'كندا' => 'كندا',
                                        'البرازيل' => 'البرازيل',
                                        'الأرجنتين' => 'الأرجنتين',
                                        'جنوب أفريقيا' => 'جنوب أفريقيا',
                                        'نيجيريا' => 'نيجيريا',
                                        'أثيوبيا' => 'أثيوبيا',
                                        'أخرى' => 'أخرى',
                                    ]),    
                            ]),
                                TextInput::make('address')
                                    ->label('العنوان التفصيلي')
                                    ->placeholder('الشارع، المبنى، الخ')
                                    ->default(null),
                            
                    ])
                    ->collapsible()->columnSpanFull(),
            ]);
    }
}
