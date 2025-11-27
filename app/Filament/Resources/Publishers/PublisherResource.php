<?php

namespace App\Filament\Resources\Publishers;

use App\Filament\Resources\Publishers\Pages\CreatePublisher;
use App\Filament\Resources\Publishers\Pages\EditPublisher;
use App\Filament\Resources\Publishers\Pages\ListPublishers;
use App\Filament\Resources\Publishers\Pages\ViewPublisher;
use App\Filament\Resources\Publishers\Schemas\PublisherForm;
use App\Filament\Resources\Publishers\Schemas\PublisherInfolist;
use App\Filament\Resources\Publishers\Tables\PublishersTable;
use App\Models\Publisher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PublisherResource extends Resource
{
    protected static ?string $model = Publisher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|UnitEnum|null $navigationGroup = 'إدارة البيانات';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'دور النشر';

    protected static ?string $modelLabel = 'دار نشر';

    protected static ?string $pluralModelLabel = 'دور النشر';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PublisherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PublisherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublishersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPublishers::route('/'),
            'create' => CreatePublisher::route('/create'),
            'view' => ViewPublisher::route('/{record}'),
            'edit' => EditPublisher::route('/{record}/edit'),
        ];
    }
}
