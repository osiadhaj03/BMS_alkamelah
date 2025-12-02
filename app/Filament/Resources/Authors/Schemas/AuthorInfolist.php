<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\IconEntry;
use Filament\Schemas\Components\ImageEntry;
use Filament\Schemas\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Schema;

class AuthorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('المعلومات الأساسية')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Group::make([
                                    ImageEntry::make('image')
                                        ->label('صورة المؤلف')
                                        ->circular()
                                        ->size(150)
                                        ->placeholder('لا توجد صورة'),
                                ])->columnSpan(1),

                                Group::make([
                                    TextEntry::make('full_name')
                                        ->label('الاسم الكامل')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->placeholder('غير محدد'),

                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('laqab')
                                                ->label('اللقب')
                                                ->badge()
                                                ->color('info')
                                                ->placeholder('لا يوجد'),

                                            TextEntry::make('kunyah')
                                                ->label('الكنية')
                                                ->badge()
                                                ->color('warning')
                                                ->placeholder('لا يوجد'),
                                        ]),

                                    TextEntry::make('madhhab')
                                        ->label('المذهب')
                                        ->badge()
                                        ->color('primary')
                                        ->placeholder('غير محدد'),

                                    IconEntry::make('is_living')
                                        ->label('على قيد الحياة')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                ])->columnSpan(2),
                            ]),

                        TextEntry::make('biography')
                            ->label('السيرة الذاتية')
                            ->html()
                            ->prose()
                            ->placeholder('لا توجد سيرة ذاتية')
                            ->columnSpanFull(),
                    ]) ->columnSpanFull(),

                Section::make('التواريخ')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('birth_date')
                                    ->label('تاريخ الولادة')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('غير معروف'),

                                TextEntry::make('death_date')
                                    ->label('تاريخ الوفاة')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('غير معروف')
                                    ->visible(fn ($record) => !$record->is_living),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('روابط الفيديو')
                    ->icon('heroicon-o-video-camera')
                    ->schema([
                        RepeatableEntry::make('video_links')
                            ->label('')
                            ->schema([
                                TextEntry::make('url')
                                    ->label('الرابط')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-link')
                                    ->copyable()
                                    ->columnSpan(2),

                                TextEntry::make('title')
                                    ->label('العنوان')
                                    ->placeholder('لا يوجد عنوان')
                                    ->columnSpan(2),

                                TextEntry::make('description')
                                    ->label('الوصف')
                                    ->placeholder('لا يوجد وصف')
                                    ->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => !empty($record->video_links)),

                Section::make('معلومات النظام')
                    ->icon('heroicon-o-cog')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('تاريخ الإنشاااء')
                                    ->dateTime('Y-m-d H:i')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime('Y-m-d H:i')
                                    ->icon('heroicon-o-arrow-path'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
