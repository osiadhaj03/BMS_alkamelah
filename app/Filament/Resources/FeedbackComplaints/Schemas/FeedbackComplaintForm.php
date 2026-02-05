<?php

namespace App\Filament\Resources\FeedbackComplaints\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class FeedbackComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات البلاغ')
                    ->schema([
                        Select::make('type')
                            ->label('النوع')
                            ->options([
                                'complaint' => 'شكوى',
                                'suggestion' => 'اقتراح',
                                'feedback' => 'ملاحظة',
                                'inquiry' => 'استفسار',
                            ])
                            ->required(),

                        Select::make('category')
                            ->label('التصنيف')
                            ->options([
                                'book' => 'كتاب',
                                'author' => 'مؤلف',
                                'search' => 'نتائج البحث',
                                'page' => 'صفحة في الموقع',
                                'general' => 'عام',
                            ])
                            ->required(),

                        TextInput::make('subject')
                            ->label('الموضوع')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('message')
                            ->label('الرسالة')
                            ->required()
                            ->rows(5)
                            ->maxLength(5000),
                    ])
                    ->columns(2),

                Section::make('معلومات المرسل')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->nullable(),
                    ])
                    ->columns(3),

                Section::make('معلومات إضافية')
                    ->schema([
                        TextInput::make('related_type')
                            ->label('نوع العنصر المرتبط')
                            ->nullable(),

                        TextInput::make('related_id')
                            ->label('معرف العنصر')
                            ->numeric()
                            ->nullable(),

                        FileUpload::make('attachment_path')
                            ->label('المرفق')
                            ->disk('public')
                            ->directory('feedback-attachments')
                            ->saveUploadedFileUsing(function ($file, $record) {
                                return Storage::disk('public')->putFile('feedback-attachments', $file);
                            })
                            ->nullable(),
                    ])
                    ->columns(3),

                Section::make('إدارة البلاغ')
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'under_review' => 'قيد المراجعة',
                                'resolved' => 'تم الحل',
                                'rejected' => 'مرفوض',
                            ])
                            ->required()
                            ->default('pending'),

                        Select::make('priority')
                            ->label('الأولوية')
                            ->options([
                                'low' => 'منخفضة',
                                'medium' => 'متوسطة',
                                'high' => 'عالية',
                                'urgent' => 'عاجلة',
                            ])
                            ->required()
                            ->default('medium'),

                        DateTimePicker::make('resolved_at')
                            ->label('تاريخ الحل')
                            ->nullable(),

                        Textarea::make('admin_notes')
                            ->label('ملاحظات الإدارة')
                            ->rows(3)
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }
}
