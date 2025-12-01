<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\BookSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'إدارة المحتوى';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('BookTabs')
                    ->tabs([
                        // Tab 1: Basic Book Information
                        Forms\Components\Tabs\Tab::make('معلومات الكتاب')
                            ->icon('heroicon-o-book-open')
                            ->schema([
                                self::getBasicInfoSection(),
                                self::getBookPropertiesSection(),
                                self::getCoverImageSection(),
                            ]),

                        // Tab 2: Categories and Authors
                        Forms\Components\Tabs\Tab::make('الأقسام والمؤلفون')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                self::getBookSectionSelect(),
                                self::getAuthorsRepeater(),
                            ]),

                        // Tab 3: Volumes and Chapters
                        Forms\Components\Tabs\Tab::make('الأجزاء والفصول')
                            ->icon('heroicon-o-folder-open')
                            ->schema([
                                self::getVolumesRepeater(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    private static function getBasicInfoSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('المعلومات الأساسية')
            ->description('أدخل العنوان والوصف والمعرف الفريد للكتاب')
            ->icon('heroicon-o-identification')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $set('slug', Str::slug($state));
                            }
                        })
                        ->columnSpan(2),
                ]),
                
                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(4)
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('المعرف')
                        ->required()
                        ->maxLength(255)
                        ->unique(Book::class, 'slug', ignoreRecord: true)
                        ->rules(['alpha_dash'])
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('generateSlug')
                                ->icon('heroicon-m-sparkles')
                                ->tooltip('توليد تلقائي من العنوان')
                                ->action(function (callable $set, callable $get) {
                                    if ($get('title')) {
                                        $set('slug', Str::slug($get('title')));
                                    }
                                })
                        ),
                    
                    Forms\Components\TextInput::make('edition')
                        ->label('رقم الطبعة')
                        ->numeric()
                        ->placeholder('1'),
                    
                    Forms\Components\TextInput::make('edition_DATA')
                        ->label(' سنة الطباعة ')
                        ->numeric()
                        ->placeholder('2025'),
                ]),
            ])
            ->collapsible();
    }

    private static function getBookPropertiesSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('خصائص الكتاب')
            ->description('الخصائص الفيزيائية والرقمية للكتاب ومعلومات النشر')
            ->icon('heroicon-o-book-open')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('publisher_id')
                        ->label('الناشر')
                        ->relationship('publisher', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(self::getPublisherForm())
                        ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                            return $action
                                ->modalHeading('إضافة ناشر جديد')
                                ->modalSubmitActionLabel('إضافة الناشر')
                                ->modalWidth('lg');
                        }),
                    
                    Forms\Components\TextInput::make('source_url')
                        ->label('رابط المصدر')
                        ->url()
                        ->placeholder('https://example.com'),
                ]),
                
                Forms\Components\Grid::make(4)->schema([
                    Forms\Components\Select::make('visibility')
                        ->label('الرؤية')
                        ->options([
                            'public' => 'عام',
                            'private' => 'خاص',
                        ])
                        ->required()
                        ->default('public'),
                    
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'draft' => 'مسودة',
                            'published' => 'منشور',
                            'archived' => 'مؤرشف',
                        ])
                        ->required()
                        ->default('draft'),
                ]),
            ])
            ->collapsible();
    }

    private static function getCoverImageSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('صورة الغلاف')
            ->description('ارفع صورة غلاف عالية الجودة للكتاب')
            ->icon('heroicon-o-photo')
            ->schema([
                Forms\Components\FileUpload::make('cover_image')
                    ->label('صورة الغلاف')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '3:4',
                        '2:3',
                    ])
                    ->maxSize(5120)
                    ->directory('books/covers')
                    ->visibility('public')
                    ->helperText('حجم أقصى: 5 ميجابايت. النسب المفضلة: 3:4 أو 2:3')
                    ->columnSpanFull(),
            ])
            ->collapsible();
    }

    private static function getBookSectionSelect(): Forms\Components\Select
    {
        return Forms\Components\Select::make('book_section_id')
            ->relationship('bookSection', 'name')
            ->label('قسم الكتاب')
            ->searchable()
            ->preload()
            ->createOptionForm(self::getBookSectionForm())
            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                return $action
                    ->modalHeading('إضافة قسم جديد')
                    ->modalSubmitActionLabel('إضافة القسم')
                    ->modalWidth('lg');
            })
            ->required();
    }

    private static function getAuthorsRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('authorBooks')
            ->label('المؤلفون ودورهم')
            ->relationship('authorBooks')
            ->schema([
                Forms\Components\Grid::make(4)->schema([
                    Forms\Components\Select::make('author_id')
                        ->label('المؤلف')
                        ->relationship('author', 'full_name')
                        ->searchable(['full_name'])
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                        ->createOptionForm(self::getAuthorForm())
                        ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                            return $action
                                ->modalHeading('إضافة مؤلف جديد')
                                ->modalSubmitActionLabel('إضافة المؤلف')
                                ->modalWidth('xl');
                        })
                        ->required()
                        ->columnSpan(2),
                    
                    Forms\Components\Select::make('role')
                        ->label('الدور')
                        ->options([
                            'author' => 'مؤلف',
                            'co_author' => 'مؤلف مشارك',
                            'editor' => 'محرر',
                            'translator' => 'مترجم',
                            'reviewer' => 'مراجع',
                            'commentator' => 'معلق',
                        ])
                        ->required()
                        ->default('author')
                        ->columnSpan(1),
                    
                    Forms\Components\Select::make('is_main')
                        ->label('مؤلف رئيسي')
                        ->options([
                            true => 'نعم',
                            false => 'لا'
                        ])
                        ->helperText('حدد المؤلف الرئيسي للكتاب')
                        ->default(true)
                        ->columnSpan(1),
                ]),
                
                Forms\Components\TextInput::make('display_order')
                    ->label('ترتيب العرض')
                    ->numeric()
                    ->default(0)
                    ->helperText('ترتيب ظهور المؤلف في قائمة المؤلفين'),
            ])
            ->addActionLabel('إضافة مؤلف')
            ->reorderableWithButtons()
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => self::getAuthorItemLabel($state))
            ->defaultItems(1)
            ->minItems(1)
            ->columnSpanFull();
    }

    private static function getVolumesRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('volumes')
            ->label('مجلدات الكتاب')
            ->relationship('volumes')
            ->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('number')
                        ->label('رقم المجلد')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(1),
                    
                    Forms\Components\TextInput::make('title')
                        ->label('عنوان المجلد')
                        ->maxLength(255)
                        ->placeholder('مثال: الجزء الأول'),
                ]),
                
                Forms\Components\Textarea::make('description')
                    ->label('وصف المجلد')
                    ->rows(2)
                    ->columnSpanFull(),

                self::getChaptersRepeater(),
            ])
            ->addActionLabel('إضافة مجلد جديد')
            ->reorderableWithButtons()
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => 
                'مجلد ' . ($state['number'] ?? 'جديد') . 
                ($state['title'] ? ' - ' . $state['title'] : '')
            )
            ->maxItems(100)
            ->defaultItems(1)
            ->columnSpanFull();
    }

    private static function getChaptersRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('chapters')
            ->label('فصول هذا المجلد')
            ->relationship('chapters')
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان الفصل')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('مثال: المقدمة')
                    ->columnSpanFull(),
                
                self::getPagesRepeater('صفحات هذا الفصل'),
            ])
            ->addActionLabel('إضافة فصل جديد')
            ->reorderableWithButtons()
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => 
                ($state['title'] ?? 'فصل جديد')
            )
            ->maxItems(200)
            ->defaultItems(0);
    }

    private static function getPagesRepeater(string $label = 'الصفحات'): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('pages')
            ->label($label)
            ->relationship('pages')
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('page_number')
                        ->label('رقم الصفحة')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('1')
                        ->helperText('رقم الصفحة في الكتاب المطبوع'),
                    
                    Forms\Components\TextInput::make('page_order')
                        ->label('ترتيب الصفحة')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->placeholder('0')
                        ->helperText('ترتيب ظهور الصفحة'),
                    
                    Forms\Components\Toggle::make('is_indexed')
                        ->label('مفهرسة')
                        ->default(false)
                        ->helperText('هل تم فهرسة الصفحة في البحث؟'),
                ]),
                
                Forms\Components\RichEditor::make('content')
                    ->label('محتوى الصفحة')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'redo',
                        'undo',
                    ])
                    ->placeholder('أدخل محتوى الصفحة هنا...')
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->rows(2)
                    ->placeholder('ملاحظات إضافية عن الصفحة (اختياري)')
                    ->columnSpanFull(),
            ])
            ->addActionLabel('إضافة صفحة جديدة')
            ->reorderableWithButtons()
            ->collapsible()
            ->collapsed()
            ->itemLabel(fn (array $state): ?string => 
                'صفحة ' . ($state['page_number'] ?? 'جديدة') . 
                (isset($state['content']) && strlen($state['content']) > 50 
                    ? ' - ' . Str::limit(strip_tags($state['content']), 30) 
                    : '')
            )
            ->maxItems(500)
            ->defaultItems(0)
            ->columnSpanFull();
    }

    private static function getAuthorForm(): array
    {
        return [
            Forms\Components\TextInput::make('full_name')
                ->label('الاسم الكامل')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('image')
                ->label('صورة المؤلف')
                ->image()
                ->directory('authors')
                ->visibility('public')
                ->columnSpanFull(),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('madhhab')
                    ->label('المذهب')
                    ->options([
                        'المذهب الحنفي' => 'المذهب الحنفي',
                        'المذهب المالكي' => 'المذهب المالكي',
                        'المذهب الشافعي' => 'المذهب الشافعي',
                        'المذهب الحنبلي' => 'المذهب الحنبلي',
                        'آخرون' => 'آخرون',
                    ])
                    ->placeholder('اختر المذهب'),
                
                Forms\Components\Select::make('is_living')
                    ->label('حالة المؤلف')
                    ->options([
                        true => 'على قيد الحياة',
                        false => 'متوفى',
                    ])
                    ->default(true)
                    ->live(),
            ]),

            Forms\Components\Textarea::make('biography')
                ->label('السيرة الذاتية')
                ->rows(4)
                ->columnSpanFull(),
            
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('birth_year_type')
                    ->label('نوع تقويم الميلاد')
                    ->options([
                        'gregorian' => 'ميلادي',
                        'hijri' => 'هجري',
                    ])
                    ->default('gregorian')
                    ->live(),
                
                Forms\Components\TextInput::make('birth_year')
                    ->label(fn ($get) => $get('birth_year_type') === 'hijri' ? 'سنة الميلاد (هجري)' : 'سنة الميلاد (ميلادي)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(fn ($get) => $get('birth_year_type') === 'hijri' ? 1500 : date('Y')),
            ]),
            
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('death_year_type')
                    ->label('نوع تقويم الوفاة')
                    ->options([
                        'gregorian' => 'ميلادي',
                        'hijri' => 'هجري',
                    ])
                    ->default('gregorian')
                    ->live()
                    ->visible(fn ($get) => !$get('is_living')),
                
                Forms\Components\TextInput::make('death_year')
                    ->label(fn ($get) => $get('death_year_type') === 'hijri' ? 'سنة الوفاة (هجري)' : 'سنة الوفاة (ميلادي)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(fn ($get) => $get('death_year_type') === 'hijri' ? 1500 : date('Y'))
                    ->visible(fn ($get) => !$get('is_living'))
                    ->nullable(),
            ]),
        ];
    }

    private static function getPublisherForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('اسم الناشر')
                ->required()
                ->maxLength(255),
            
            Forms\Components\TextInput::make('address')
                ->label('العنوان')
                ->maxLength(255),
            
            Forms\Components\TextInput::make('phone')
                ->label('رقم الهاتف')
                ->tel()
                ->maxLength(20),
            
            Forms\Components\TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->maxLength(255),
            
            Forms\Components\TextInput::make('website_url')
                ->label('الموقع الإلكتروني')
                ->url()
                ->maxLength(255),
            
            Forms\Components\Textarea::make('description')
                ->label('وصف الناشر')
                ->rows(3)
                ->maxLength(1000)
                ->columnSpanFull(),
            
            Forms\Components\FileUpload::make('image')
                ->label('صورة الناشر')
                ->image()
                ->imageEditor()
                ->maxSize(2048)
                ->directory('publishers')
                ->visibility('public')
                ->columnSpanFull(),
        ];
    }

    private static function getBookSectionForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('اسم القسم')
                ->required()
                ->maxLength(255),
            
            Forms\Components\TextInput::make('slug')
                ->label('الرابط الثابت')
                ->maxLength(255)
                ->unique(BookSection::class, 'slug', ignoreRecord: true)
                ->rules(['alpha_dash']),
            
            Forms\Components\Textarea::make('description')
                ->label('وصف القسم')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    private static function getAuthorItemLabel(array $state): ?string
    {
        if (!isset($state['author_id'])) {
            return 'مؤلف جديد';
        }
        
        $author = Author::find($state['author_id']);
        $role = $state['role'] ?? 'author';
        
        $roleLabels = [
            'author' => 'مؤلف',
            'co_author' => 'مؤلف مشارك',
            'editor' => 'محرر',
            'translator' => 'مترجم',
            'reviewer' => 'مراجع',
            'commentator' => 'معلق',
        ];
        
        return ($author ? $author->full_name : 'غير محدد') . ' - ' . ($roleLabels[$role] ?? $role);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان الكتاب')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('mainAuthors')
                    ->label('المؤلف الرئيسي')
                    ->getStateUsing(function ($record) {
                        $mainAuthor = $record->authorBooks
                            ->where('is_main', true)
                            ->first();
                        
                        if ($mainAuthor && $mainAuthor->author) {
                            return $mainAuthor->author->full_name;
                        }
                        
                        $firstAuthor = $record->authorBooks->first();
                        return $firstAuthor?->author?->full_name ?? 'غير محدد';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('authorBooks.author', function (Builder $query) use ($search) {
                            $query->where('full_name', 'like', "%{$search}%");
                        });
                    })
                    ->limit(30)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('bookSection.name')
                    ->label('القسم')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'archived' => 'مؤرشف',
                        default => $state,
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('publisher.name')
                    ->label('الناشر')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('edition')
                    ->label('الطبعة')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (?int $state): string => $state ? "الطبعة {$state}" : 'غير محدد')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('volumes_count')
                    ->label('المجلدات')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(fn ($record) => $record->volumes_count ?? 0)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('pages_count')
                    ->label('الصفحات')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn ($record) => $record->pages_count ?? 0)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('visibility')
                    ->label('الرؤية')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'private' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'public' => 'عام',
                        'private' => 'خاص',
                        default => $state,
                    })
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'published' => 'منشور',
                        'archived' => 'مؤرشف',
                    ]),
                
                Tables\Filters\SelectFilter::make('visibility')
                    ->label('الرؤية')
                    ->options([
                        'public' => 'عام',
                        'private' => 'خاص',
                    ]),
                
                Tables\Filters\SelectFilter::make('book_section_id')
                    ->label('القسم')
                    ->relationship('bookSection', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات الكتاب')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('العنوان'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('الوصف'),
                        Infolists\Components\TextEntry::make('slug')
                            ->label('المعرف'),
                    ])
                    ->columns(2),
            ]);
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
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
