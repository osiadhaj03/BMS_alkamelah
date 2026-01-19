<?php

namespace App\Filament\Pages;

use App\Models\Author;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Page;
use App\Models\Volume;
use App\Services\MetadataParserService;
use App\Services\TurathScraperService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

/**
 * صفحة استيراد الكتب من Turath.io
 */
class ImportTurathBook extends FilamentPage implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.import-turath-book';

    public function getTitle(): string
    {
        return 'استيراد كتاب من Turath.io';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cloud-arrow-down';
    }

    public static function getNavigationLabel(): string
    {
        return 'استيراد من تراث';
    }

    public static function getNavigationSort(): ?int
    {
        return 100;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'أدوات';
    }

    // حالة الاستيراد
    public bool $isImporting = false;
    public int $progress = 0;
    public int $totalPages = 0;
    public int $importedPages = 0;
    public string $statusMessage = '';
    public array $importLog = [];

    // بيانات النموذج
    public string $bookUrl = '';
    public bool $skipPages = false;
    public bool $forceReimport = false;

    // بيانات الكتاب المستخرجة
    public ?array $bookInfo = null;
    public ?array $parsedInfo = null;

    /**
     * تعريف النموذج
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('رابط الكتاب')
                    ->description('أدخل رابط الكتاب من موقع Turath.io')
                    ->schema([
                        TextInput::make('bookUrl')
                            ->label('رابط الكتاب أو معرف الكتاب')
                            ->placeholder('https://app.turath.io/book/147927 أو 147927')
                            ->required()
                            ->helperText('يمكنك إدخال الرابط الكامل أو معرف الكتاب فقط')
                            ->disabled(fn() => $this->isImporting),

                        Toggle::make('skipPages')
                            ->label('استيراد بدون الصفحات')
                            ->helperText('استيراد معلومات الكتاب والفهرس فقط')
                            ->disabled(fn() => $this->isImporting),

                        Toggle::make('forceReimport')
                            ->label('إعادة الاستيراد')
                            ->helperText('حذف الكتاب إذا كان موجوداً وإعادة استيراده')
                            ->disabled(fn() => $this->isImporting),
                    ])
                    ->columns(1),
            ]);
    }

    /**
     * استخراج معرف الكتاب من الرابط
     */
    protected function extractBookId(string $input): ?int
    {
        $input = trim($input);

        // إذا كان رقماً مباشرة
        if (is_numeric($input)) {
            return (int) $input;
        }

        // استخراج من رابط
        // https://app.turath.io/book/147927
        // https://turath.io/book/147927
        if (preg_match('/book\/(\d+)/i', $input, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * معاينة الكتاب قبل الاستيراد
     */
    public function previewBook(): void
    {
        $bookId = $this->extractBookId($this->bookUrl);

        if (!$bookId) {
            Notification::make()
                ->title('خطأ')
                ->body('الرجاء إدخال رابط أو معرف صحيح')
                ->danger()
                ->send();
            return;
        }

        $this->statusMessage = 'جاري جلب معلومات الكتاب...';
        $this->addLog('📡 جاري جلب معلومات الكتاب...');

        $scraper = app(TurathScraperService::class);
        $parser = app(MetadataParserService::class);

        $this->bookInfo = $scraper->getBookInfo($bookId);

        if (!$this->bookInfo || !isset($this->bookInfo['meta'])) {
            Notification::make()
                ->title('خطأ')
                ->body('فشل جلب معلومات الكتاب. تأكد من صحة المعرف.')
                ->danger()
                ->send();
            $this->statusMessage = '';
            return;
        }

        // تحليل البيانات
        $this->parsedInfo = $parser->parseBookInfo($this->bookInfo['meta']['info'] ?? '');

        // حساب عدد الصفحات
        $volumeBounds = $this->bookInfo['indexes']['volume_bounds'] ?? [];
        $this->totalPages = $scraper->getTotalPages($volumeBounds);

        $this->statusMessage = '';
        $this->addLog("✅ تم جلب معلومات الكتاب: {$this->bookInfo['meta']['name']}");

        Notification::make()
            ->title('تم جلب معلومات الكتاب')
            ->body($this->bookInfo['meta']['name'])
            ->success()
            ->send();
    }

    /**
     * بدء عملية الاستيراد
     */
    public function startImport(): void
    {
        $bookId = $this->extractBookId($this->bookUrl);

        if (!$bookId) {
            Notification::make()
                ->title('خطأ')
                ->body('الرجاء إدخال رابط صحيح')
                ->danger()
                ->send();
            return;
        }

        // التحقق من وجود الكتاب
        $existingBook = Book::where('shamela_id', $bookId)->first();
        if ($existingBook && !$this->forceReimport) {
            Notification::make()
                ->title('الكتاب موجود')
                ->body("الكتاب موجود مسبقاً: {$existingBook->title}")
                ->warning()
                ->send();
            return;
        }

        $this->isImporting = true;
        $this->progress = 0;
        $this->importedPages = 0;
        $this->importLog = [];

        $this->addLog('🚀 بدء عملية الاستيراد...');

        // تنفيذ الاستيراد
        $this->performImport($bookId, $existingBook);
    }

    /**
     * تنفيذ عملية الاستيراد
     */
    protected function performImport(int $bookId, ?Book $existingBook): void
    {
        set_time_limit(0); // إلغاء حد وقت التنفيذ للعمليات الطويلة
        $scraper = app(TurathScraperService::class);
        $parser = app(MetadataParserService::class);

        try {
            // جلب معلومات الكتاب إذا لم تكن موجودة
            if (!$this->bookInfo) {
                $this->addLog('📡 جاري جلب معلومات الكتاب...');
                $this->bookInfo = $scraper->getBookInfo($bookId);

                if (!$this->bookInfo) {
                    throw new \Exception('فشل جلب معلومات الكتاب');
                }
            }

            $meta = $this->bookInfo['meta'];
            $indexes = $this->bookInfo['indexes'] ?? [];

            // تحليل البيانات
            $parsedInfo = $parser->parseBookInfo($meta['info'] ?? '');
            $authorData = $parser->extractAuthorDates($parsedInfo['author_name'] ?? '');

            // حساب المجلدات والفصول
            $volumeBounds = $indexes['volume_bounds'] ?? [];
            $headings = $indexes['headings'] ?? [];
            $volumes = $scraper->parseVolumes($volumeBounds);
            $chapters = $scraper->parseChapters($headings);
            $this->totalPages = $scraper->getTotalPages($volumeBounds);

            $this->addLog("📖 اسم الكتاب: {$meta['name']}");
            $this->addLog("👤 المؤلف: " . ($parsedInfo['author_name'] ?? 'غير معروف'));
            $this->addLog("📚 المجلدات: " . count($volumes));
            $this->addLog("📑 الفصول: " . count($chapters));
            $this->addLog("📄 الصفحات: {$this->totalPages}");

            $createdBook = DB::transaction(function () use ($bookId, $meta, $parsedInfo, $authorData, $volumes, $chapters, $existingBook, $scraper, $parser) {
                // حذف الكتاب القديم
                if ($existingBook && $this->forceReimport) {
                    $this->addLog('🗑️ حذف الكتاب القديم...');
                    $existingBook->pages()->delete();
                    $existingBook->chapters()->delete();
                    $existingBook->volumes()->delete();
                    $existingBook->authors()->detach();
                    $existingBook->delete();
                }

                // إنشاء المؤلف
                $author = $this->findOrCreateAuthor($authorData, $parsedInfo);
                if ($author) {
                    $this->addLog("✅ المؤلف: {$author->full_name}");
                }

                // إنشاء المحقق
                $editor = null;
                if (!empty($parsedInfo['editor_name'])) {
                    $editorData = $parser->extractAuthorDates($parsedInfo['editor_name']);
                    $editor = $this->findOrCreateAuthor($editorData, ['author_name' => $parsedInfo['editor_name']]);
                    if ($editor) {
                        $this->addLog("✅ المحقق: {$editor->full_name}");
                    }
                }

                // إنشاء الكتاب
                $book = $this->createBook($bookId, $meta, $parser);
                $this->addLog("✅ تم إنشاء الكتاب");

                // ربط المؤلف
                if ($author) {
                    $book->authors()->attach($author->id, [
                        'role' => 'author',
                        'is_main' => true,
                        'display_order' => 1,
                    ]);
                }

                // ربط المحقق
                if ($editor) {
                    $book->authors()->attach($editor->id, [
                        'role' => 'editor',
                        'is_main' => false,
                        'display_order' => 2,
                    ]);
                }

                // إنشاء المجلدات
                $volumeModels = $this->createVolumes($book, $volumes);
                $this->addLog("✅ تم إنشاء " . count($volumeModels) . " مجلد");

                // إنشاء الفصول
                $this->createChapters($book, $chapters, $volumeModels);
                $this->addLog("✅ تم إنشاء " . count($chapters) . " فصل");

                return ['book' => $book, 'volumeModels' => $volumeModels];
            });

            // استيراد الصفحات خارج المعاملة الأساسية لتفادي timeout قاعدة البيانات
            if (!$this->skipPages && $this->totalPages > 0) {
                $this->addLog("📄 جاري استيراد الصفحات...");
                $this->importPages($createdBook['book'], $bookId, $createdBook['volumeModels'], $scraper);
            }

            $this->addLog('');
            $this->addLog('═══════════════════════════════════════');
            $this->addLog('✅ تم الاستيراد بنجاح!');
            $this->addLog("📖 الكتاب: {$meta['name']}");
            $this->addLog("📄 الصفحات المستوردة: {$this->importedPages}");
            $this->addLog('═══════════════════════════════════════');

            $this->progress = 100;

            Notification::make()
                ->title('تم الاستيراد بنجاح!')
                ->body("تم استيراد {$this->importedPages} صفحة")
                ->success()
                ->send();

        } catch (\Exception $e) {
            $this->addLog("❌ خطأ: {$e->getMessage()}");

            Notification::make()
                ->title('فشل الاستيراد')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        $this->isImporting = false;
        $this->bookInfo = null;
        $this->parsedInfo = null;
    }

    /**
     * البحث عن مؤلف أو إنشاء جديد
     */
    protected function findOrCreateAuthor(array $authorData, array $parsedInfo): ?Author
    {
        $fullName = $authorData['clean_name'] ?? $parsedInfo['author_name'] ?? null;

        if (empty($fullName)) {
            return null;
        }

        return Author::firstOrCreate(
            ['full_name' => $fullName],
            [
                'is_living' => false,
                'birth_date' => $authorData['birth_year']
                    ? "{$authorData['birth_year']}-01-01"
                    : null,
                'death_date' => $authorData['death_year']
                    ? "{$authorData['death_year']}-01-01"
                    : null,
            ]
        );
    }

    /**
     * إنشاء الكتاب
     */
    protected function createBook(int $turathId, array $meta, MetadataParserService $parser): Book
    {
        $title = $parser->cleanBookName($meta['name']);

        return Book::create([
            'shamela_id' => (string) $turathId,
            'title' => $title,
            'description' => $meta['info'] ?? null,
            'visibility' => 'public',
            'has_original_pagination' => true,
        ]);
    }

    /**
     * إنشاء المجلدات
     */
    protected function createVolumes(Book $book, array $volumes): array
    {
        $volumeModels = [];

        if (empty($volumes)) {
            $volumeModels[1] = Volume::create([
                'book_id' => $book->id,
                'number' => 1,
            ]);
        } else {
            foreach ($volumes as $volumeData) {
                $volume = Volume::create([
                    'book_id' => $book->id,
                    'number' => $volumeData['number'],
                    'page_start' => $volumeData['page_start'],
                    'page_end' => $volumeData['page_end'],
                ]);
                $volumeModels[$volumeData['number']] = $volume;
            }
        }

        return $volumeModels;
    }

    /**
     * إنشاء الفصول
     */
    protected function createChapters(Book $book, array $chapters, array $volumeModels): void
    {
        foreach ($chapters as $chapterData) {
            $volumeId = null;
            if ($chapterData['page_start']) {
                foreach ($volumeModels as $num => $volume) {
                    if ($volume->page_start && $volume->page_end) {
                        if (
                            $chapterData['page_start'] >= $volume->page_start
                            && $chapterData['page_start'] <= $volume->page_end
                        ) {
                            $volumeId = $volume->id;
                            break;
                        }
                    }
                }
            }

            $volumeId = $volumeId ?? reset($volumeModels)?->id;

            Chapter::create([
                'book_id' => $book->id,
                'volume_id' => $volumeId,
                'title' => $chapterData['title'],
                'level' => $chapterData['level'],
                'order' => $chapterData['order'],
                'page_start' => $chapterData['page_start'],
            ]);
        }
    }

    /**
     * استيراد الصفحات
     */
    protected function importPages(Book $book, int $turathBookId, array $volumeModels, TurathScraperService $scraper): void
    {
        $pages = [];
        $batchSize = 25; // تقليل حجم الدفعة لضمان تكرار التواصل مع قاعدة البيانات

        foreach ($scraper->getAllPages($turathBookId, 1, $this->totalPages) as $pageData) {
            // تحديد المجلد
            $volumeId = null;
            foreach ($volumeModels as $num => $volume) {
                if ($volume->page_start && $volume->page_end) {
                    if (
                        $pageData['page_number'] >= $volume->page_start
                        && $pageData['page_number'] <= $volume->page_end
                    ) {
                        $volumeId = $volume->id;
                        break;
                    }
                }
            }
            $volumeId = $volumeId ?? reset($volumeModels)?->id;

            $pages[] = [
                'book_id' => $book->id,
                'volume_id' => $volumeId,
                'page_number' => $pageData['page_number'],
                'content' => $pageData['content'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->importedPages++;

            // تحديث التقدم
            if ($this->totalPages > 0) {
                $this->progress = (int) (($this->importedPages / $this->totalPages) * 100);
            }

            // حفظ دفعة
            if (count($pages) >= $batchSize) {
                // التأكد من أن الاتصال بقاعدة البيانات لا يزال قائماً
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    DB::reconnect();
                }

                Page::insert($pages);
                $pages = [];
                $this->addLog("📄 تم استيراد {$this->importedPages} صفحة...");
            }
        }

        // حفظ الباقي
        if (!empty($pages)) {
            Page::insert($pages);
        }
    }

    /**
     * إضافة سجل
     */
    protected function addLog(string $message): void
    {
        $this->importLog[] = [
            'time' => now()->format('H:i:s'),
            'message' => $message,
        ];
    }

    /**
     * إلغاء الاستيراد
     */
    public function cancelImport(): void
    {
        $this->isImporting = false;
        $this->addLog('⚠️ تم إلغاء الاستيراد');

        Notification::make()
            ->title('تم إلغاء الاستيراد')
            ->warning()
            ->send();
    }

    /**
     * إعادة تعيين النموذج
     */
    public function resetForm(): void
    {
        $this->bookUrl = '';
        $this->skipPages = false;
        $this->forceReimport = false;
        $this->bookInfo = null;
        $this->parsedInfo = null;
        $this->progress = 0;
        $this->importedPages = 0;
        $this->totalPages = 0;
        $this->importLog = [];
        $this->statusMessage = '';
    }

    /**
     * الحصول على إجراءات الصفحة
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('معاينة')
                ->icon('heroicon-o-eye')
                ->action('previewBook')
                ->disabled(fn() => $this->isImporting || empty($this->bookUrl)),

            Action::make('import')
                ->label('بدء الاستيراد')
                ->icon('heroicon-o-cloud-arrow-down')
                ->action('startImport')
                ->color('success')
                ->disabled(fn() => $this->isImporting || empty($this->bookUrl)),

            Action::make('reset')
                ->label('إعادة تعيين')
                ->icon('heroicon-o-arrow-path')
                ->action('resetForm')
                ->color('gray')
                ->disabled(fn() => $this->isImporting),
        ];
    }
}
