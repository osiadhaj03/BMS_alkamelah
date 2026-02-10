<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات المستخدم')
                    ->schema([
                        TextEntry::make('name')
                            ->label('الاسم'),
                        TextEntry::make('email')
                            ->label('البريد الإلكتروني'),
                        TextEntry::make('email_verified_at')
                            ->label('تاريخ التحقق من البريد')
                            ->dateTime()
                            ->placeholder('لم يتم التحقق'),
                        TextEntry::make('roles.name')
                            ->label('الصلاحيات')
                            ->badge()
                            ->separator(','),
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('تاريخ آخر تحديث')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
