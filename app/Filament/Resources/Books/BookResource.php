<?php

namespace App\Filament\Resources\Books;

use App\Filament\Resources\Books\Pages\CreateBook;
use App\Filament\Resources\Books\Pages\EditBook;
use App\Filament\Resources\Books\Pages\ListBooks;
use App\Filament\Resources\Books\Pages\ViewBook;
use App\Filament\Resources\Books\Schemas\BookForm;
use App\Filament\Resources\Books\Schemas\BookInfolist;
use App\Filament\Resources\Books\Tables\BooksTable;
use App\Models\Book;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'إدارة المحتوى';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'الكتب';

    protected static ?string $modelLabel = 'كتاب';

    protected static ?string $pluralModelLabel = 'الكتب';

    protected static ?string $recordTitleAttribute = 'title';

    /**
     * Optimize queries to avoid N+1 Query Problem
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['volumes', 'pages'])
            ->with([
                'bookSection',
                'publisher',
                'authorBooks' => function ($query) {
                    $query->with('author')->orderBy('display_order');
                }
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return BookForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BooksTable::configure($table);
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
            'index' => ListBooks::route('/'),
            'create' => CreateBook::route('/create'),
            'view' => ViewBook::route('/{record}'),
            'edit' => EditBook::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description', 'slug'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $mainAuthor = $record->authorBooks->where('is_main', true)->first()
            ?? $record->authorBooks->first();
        
        return [
            'المؤلف' => $mainAuthor?->author?->full_name ?? 'غير محدد',
            'القسم' => $record->bookSection?->name ?? 'غير محدد',
            'الناشر' => $record->publisher?->name ?? 'غير محدد',
        ];
    }
}
