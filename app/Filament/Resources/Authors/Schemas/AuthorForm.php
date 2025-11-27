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
                TextInput::make('slug')
                    ->default(null),
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
                Select::make('birth_year_type')
                    ->options(['gregorian' => 'Gregorian', 'hijri' => 'Hijri'])
                    ->default('gregorian')
                    ->required(),
                TextInput::make('birth_year')
                    ->numeric()
                    ->default(null),
                Select::make('death_year_type')
                    ->options(['gregorian' => 'Gregorian', 'hijri' => 'Hijri'])
                    ->default('gregorian'),
                TextInput::make('death_year')
                    ->numeric()
                    ->default(null),
                DatePicker::make('birth_date'),
                DatePicker::make('death_date'),
                TextInput::make('author_role')
                    ->default('مؤلف'),
            ]);
    }
}
