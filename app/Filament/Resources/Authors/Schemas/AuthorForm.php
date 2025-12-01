<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                Textarea::make('biography')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image(),
                Select::make('madhhab')
                    ->options([
            'المذهب الحنفي' => 'المذهبالحنفي',
            'المذهب المالكي' => 'المذهبالمالكي',
            'المذهب الشافعي' => 'المذهبالشافعي',
            'المذهب الحنبلي' => 'المذهبالحنبلي',
            'آخرون' => 'آخرون',
        ])
                    ->default(null),
                Toggle::make('is_living')
                    ->required(),
                DatePicker::make('birth_date'),
                DatePicker::make('death_date'),
            ]);
    }
}
