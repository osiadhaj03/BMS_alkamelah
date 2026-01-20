<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\BookSection;
use App\Services\TurathScraperService;
use App\Services\MetadataParserService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('استيراد قسم كامل من تراث')]
class ImportCategoryPage extends Component
{
    // Input
    public string $categoryUrl = '';
    public string $manualBookIds = '';
    public ?int $sectionId = null;
    public array $sections = [];

    // Books list
    public array $books = [];
    public int $currentBookIndex = 0;

    // Import state
    public bool $isImporting = false;
    public bool $isFetching = false;
    public int $progress = 0;
    public int $completedBooks = 0;
    public int $failedBooks = 0;

    // Current book import state (for incremental import)
    public bool $isImportingCurrentBook = false;
    public ?string $currentBookImportState = null; // 'init', 'pages', 'done'
    public int $currentBookPageOffset = 0;
    public array $currentBookData = [];

    // Logs
    public array $importLog = [];
    public string $statusMessage = '';

    // Options
    public bool $skipPages = false;
    public bool $forceReimport = false;

    public function mount()
    {
        $this->sections = BookSection::pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.import-category-page')
            ->layout('components.layouts.app');
    }

    /**
     * Load books from manual IDs input
     */
    public function loadBooks()
    {
        // Increase time limit for this operation
        set_time_limit(120);

        $this->isFetching = true;
        $this->books = [];
        $this->importLog = [];
        $this->statusMessage = '';

        // Parse IDs from input
        $input = $this->manualBookIds;
        $ids = preg_split('/[\s,\n\r]+/', $input);
        $ids = array_filter($ids, fn($id) => is_numeric(trim($id)));
        $ids = array_map('trim', $ids);
        $ids = array_unique($ids);

        if (empty($ids)) {
            $this->statusMessage = 'لم يتم العثور على IDs صالحة. أدخل أرقام الكتب مفصولة بفاصلة أو سطر جديد.';
            $this->isFetching = false;
            return;
        }

        $this->addLog("📚 تم العثور على " . count($ids) . " كتاب");

        foreach ($ids as $index => $bookId) {
            $this->addLog("📖 جاري جلب معلومات الكتاب {$bookId} (" . ($index + 1) . "/" . count($ids) . ")...");

            try {
                $response = Http::timeout(15)->get("https://api.turath.io/book", [
                    'id' => $bookId,
                    'ver' => 3
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $meta = $data['meta'] ?? [];
                    $indexes = $data['indexes'] ?? [];

                    // Extract author from info
                    $authorName = '؟';
                    if (!empty($meta['info'])) {
                        if (preg_match('/المؤلف:\s*([^\n]+)/', $meta['info'], $match)) {
                            $authorName = trim($match[1]);
                            $authorName = explode('(', $authorName)[0];
                            $authorName = mb_substr($authorName, 0, 40);
                        }
                    }

                    // Calculate pages
                    $pages = 0;
                    if (!empty($indexes['page_map'])) {
                        $pages = count($indexes['page_map']);
                    } elseif (!empty($indexes['volume_bounds'])) {
                        foreach ($indexes['volume_bounds'] as $bounds) {
                            $pages += ($bounds[1] - $bounds[0] + 1);
                        }
                    }

                    $this->books[] = [
                        'id' => $bookId,
                        'name' => $meta['name'] ?? "كتاب {$bookId}",
                        'author' => $authorName,
                        'pages' => $pages ?: '؟',
                        'status' => 'pending', // pending, importing, done, error
                        'message' => '',
                    ];

                    $this->addLog("✅ " . ($meta['name'] ?? $bookId));
                } else {
                    $this->books[] = [
                        'id' => $bookId,
                        'name' => "كتاب {$bookId}",
                        'author' => '؟',
                        'pages' => '؟',
                        'status' => 'pending',
                        'message' => '',
                    ];
                    $this->addLog("⚠️ لم يتم جلب معلومات الكتاب {$bookId}");
                }
            } catch (\Exception $e) {
                $this->books[] = [
                    'id' => $bookId,
                    'name' => "كتاب {$bookId}",
                    'author' => '؟',
                    'pages' => '؟',
                    'status' => 'pending',
                    'message' => '',
                ];
                $this->addLog("❌ خطأ في جلب الكتاب {$bookId}: " . $e->getMessage());
            }
        }

        $this->addLog("📊 تم تحميل " . count($this->books) . " كتاب");
        $this->isFetching = false;
    }

    /**
     * Start batch import
     */
    public function startImport()
    {
        if (empty($this->books)) {
            $this->statusMessage = 'لا توجد كتب للاستيراد. قم بتحميل الكتب أولاً.';
            return;
        }

        $this->isImporting = true;
        $this->currentBookIndex = 0;
        $this->completedBooks = 0;
        $this->failedBooks = 0;
        $this->progress = 0;
        $this->isImportingCurrentBook = false;
        $this->currentBookImportState = null;

        $this->addLog("🚀 بدء عملية الاستيراد...");
    }

    /**
     * Import next batch (called by wire:poll) - handles one step at a time
     */
    public function importNextBook()
    {
        // Increase time limit for each poll cycle
        set_time_limit(60);

        if (!$this->isImporting) {
            return;
        }

        // Check if we've finished all books
        if ($this->currentBookIndex >= count($this->books)) {
            $this->finishImport();
            return;
        }

        $book = &$this->books[$this->currentBookIndex];

        // Skip already processed books
        if ($book['status'] === 'done' || $book['status'] === 'error') {
            $this->currentBookIndex++;
            $this->updateProgress();
            return;
        }

        try {
            // State machine for importing current book
            if (!$this->isImportingCurrentBook) {
                // Start importing new book
                $this->startImportingBook($book);
            } else {
                // Continue importing current book
                $this->continueImportingBook($book);
            }
        } catch (\Exception $e) {
            $book['status'] = 'error';
            $book['message'] = mb_substr($e->getMessage(), 0, 100);
            $this->addLog("❌ فشل استيراد: {$book['name']} - " . $e->getMessage());
            $this->failedBooks++;
            $this->isImportingCurrentBook = false;
            $this->currentBookIndex++;
            $this->updateProgress();
        }
    }

    /**
     * Start importing a new book
     */
    protected function startImportingBook(array &$book)
    {
        $book['status'] = 'importing';
        $this->addLog("📖 استيراد: {$book['name']} ({$book['id']})");

        // Check if book already exists
        $existingBook = Book::where('shamela_id', (string) $book['id'])->first();

        if ($existingBook && !$this->forceReimport) {
            $book['status'] = 'done';
            $book['message'] = 'موجود مسبقاً';
            $this->addLog("⏭️ الكتاب موجود مسبقاً: {$book['name']}");
            $this->completedBooks++;
            $this->currentBookIndex++;
            $this->updateProgress();
            return;
        }

        // Delete existing book if force reimport
        if ($existingBook && $this->forceReimport) {
            $this->addLog("🗑️ حذف الكتاب الموجود: {$book['name']}");
            $existingBook->pages()->delete();
            $existingBook->chapters()->delete();
            $existingBook->volumes()->delete();
            $existingBook->delete();
        }

        // Fetch book data from Turath API
        $this->addLog("📡 جاري جلب معلومات الكتاب...");

        $response = Http::timeout(30)->get("https://api.turath.io/book", [
            'id' => $book['id'],
            'ver' => 3
        ]);

        if (!$response->successful()) {
            throw new \Exception("فشل في جلب بيانات الكتاب من API");
        }

        $data = $response->json();
        $meta = $data['meta'] ?? [];
        $indexes = $data['indexes'] ?? [];

        // Store book data for incremental import
        $this->currentBookData = [
            'api_data' => $data,
            'book_id' => null,
            'total_pages' => 0,
            'imported_pages' => 0,
        ];

        // Calculate total pages
        $volumeBounds = $indexes['volume_bounds'] ?? [];
        $totalPages = 0;
        foreach ($volumeBounds as $bounds) {
            $totalPages += ($bounds[1] - $bounds[0] + 1);
        }
        $this->currentBookData['total_pages'] = $totalPages;

        $this->addLog("📖 اسم الكتاب: " . ($meta['name'] ?? 'غير معروف'));
        $this->addLog("📚 المجلدات: " . count($volumeBounds));
        $this->addLog("📄 الصفحات: {$totalPages}");

        // Create book and metadata
        $this->createBookFromData($book, $data);

        // Set state for page import
        $this->isImportingCurrentBook = true;
        $this->currentBookImportState = $this->skipPages ? 'done' : 'pages';
        $this->currentBookPageOffset = 1;
    }

    /**
     * Continue importing current book (pages batch)
     */
    protected function continueImportingBook(array &$book)
    {
        if ($this->currentBookImportState === 'pages') {
            $this->importPagesBatch($book);
        } elseif ($this->currentBookImportState === 'done') {
            $this->finishCurrentBook($book);
        }
    }

    /**
     * Import a batch of pages
     */
    protected function importPagesBatch(array &$book)
    {
        $bookId = $this->currentBookData['book_id'];
        $totalPages = $this->currentBookData['total_pages'];
        $batchSize = 50; // Pages per poll cycle

        if (!$bookId) {
            $this->currentBookImportState = 'done';
            return;
        }

        $startPage = $this->currentBookPageOffset;
        $endPage = min($startPage + $batchSize - 1, $totalPages);

        if ($startPage > $totalPages) {
            $this->currentBookImportState = 'done';
            return;
        }

        $this->addLog("📄 جاري استيراد الصفحات {$startPage}-{$endPage}...");

        $dbBook = Book::find($bookId);
        if (!$dbBook) {
            throw new \Exception("الكتاب غير موجود في قاعدة البيانات");
        }

        $pagesData = [];
        $apiData = $this->currentBookData['api_data'];
        $volumeBounds = $apiData['indexes']['volume_bounds'] ?? [];
        $pageMap = $apiData['indexes']['page_map'] ?? [];
        $headings = $apiData['indexes']['headings'] ?? [];

        // Build heading lookup
        $pageHeadings = [];
        foreach ($headings as $heading) {
            $pg = $heading['pg'] ?? null;
            if ($pg) {
                $pageHeadings[$pg] = $heading['title'] ?? '';
            }
        }

        // Fetch pages in batch
        for ($pg = $startPage; $pg <= $endPage; $pg++) {
            try {
                $pageResponse = Http::timeout(10)->get("https://api.turath.io/page", [
                    'book_id' => $book['id'],
                    'pg' => $pg,
                    'ver' => 3
                ]);

                if ($pageResponse->successful()) {
                    $pageData = $pageResponse->json();
                    $text = $pageData['text'] ?? '';
                    $pageMeta = json_decode($pageData['meta'] ?? '{}', true);

                    // Determine volume number
                    $volumeNumber = 1;
                    foreach ($volumeBounds as $volIndex => $bounds) {
                        if ($pg >= $bounds[0] && $pg <= $bounds[1]) {
                            $volumeNumber = $volIndex + 1;
                            break;
                        }
                    }

                    // Get original page number from page_map
                    $originalPageNum = $pageMap[$pg - 1] ?? null;

                    // Get chapter title if exists
                    $chapterTitle = $pageHeadings[$pg] ?? null;

                    $pagesData[] = [
                        'book_id' => $bookId,
                        'volume_number' => $volumeNumber,
                        'page_number' => $pg,
                        'original_page_number' => $originalPageNum,
                        'content' => $text,
                        'chapter_title' => $chapterTitle,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            } catch (\Exception $e) {
                // Skip failed pages
            }

            // Small delay to avoid rate limiting
            usleep(50000); // 50ms
        }

        // Bulk insert pages
        if (!empty($pagesData)) {
            \App\Models\Page::insert($pagesData);
            $this->addLog("✅ تم استيراد دفعة {$startPage}-{$endPage} (" . count($pagesData) . " صفحة)");
        }

        $this->currentBookPageOffset = $endPage + 1;
        $this->currentBookData['imported_pages'] = $endPage;

        // Check if we're done
        if ($this->currentBookPageOffset > $totalPages) {
            $this->currentBookImportState = 'done';
        }
    }

    /**
     * Create book record from API data
     */
    protected function createBookFromData(array &$book, array $apiData)
    {
        $meta = $apiData['meta'] ?? [];
        $indexes = $apiData['indexes'] ?? [];

        // Parse metadata
        $metadataParser = new MetadataParserService();
        $parsedMeta = $metadataParser->parseBookInfo($meta['info'] ?? '');

        // Create or find author
        $authorName = $parsedMeta['author_name'] ?? 'مؤلف غير معروف';
        $author = \App\Models\Author::firstOrCreate(
            ['name' => $authorName],
            ['bio' => '', 'death_year' => null]
        );

        if ($author->wasRecentlyCreated) {
            $this->addLog("✅ المؤلف: {$authorName}");
        } else {
            $this->addLog("✅ المؤلف (موجود): {$authorName}");
        }

        // Handle editor/reviewer
        if (!empty($parsedMeta['editor_name'])) {
            $this->addLog("✅ المحقق: " . $parsedMeta['editor_name']);
        }

        // Calculate total pages
        $volumeBounds = $indexes['volume_bounds'] ?? [];
        $totalPages = 0;
        foreach ($volumeBounds as $bounds) {
            $totalPages += ($bounds[1] - $bounds[0] + 1);
        }

        // Create book
        $newBook = Book::create([
            'title' => $meta['name'] ?? "كتاب {$book['id']}",
            'short_title' => mb_substr($meta['name'] ?? '', 0, 100),
            'description' => $meta['info'] ?? '',
            'book_section_id' => $this->sectionId,
            'shamela_id' => (string) $book['id'],
            'source' => 'turath.io',
            'is_active' => true,
        ]);

        // Attach author
        $newBook->authors()->attach($author->id, ['role' => 'author', 'display_order' => 1]);

        // Attach editor if exists
        if (!empty($parsedMeta['editor_name'])) {
            $editor = \App\Models\Author::firstOrCreate(
                ['name' => $parsedMeta['editor_name']],
                ['bio' => '', 'death_year' => null]
            );
            $newBook->authors()->attach($editor->id, ['role' => 'editor', 'display_order' => 2]);
        }

        // Create volumes
        foreach ($volumeBounds as $volIndex => $bounds) {
            \App\Models\Volume::create([
                'book_id' => $newBook->id,
                'number' => $volIndex + 1,
                'title' => 'الجزء ' . ($volIndex + 1),
                'start_page' => $bounds[0],
                'end_page' => $bounds[1],
            ]);
        }
        $this->addLog("✅ تم إنشاء " . count($volumeBounds) . " مجلد");

        // Create chapters from headings
        $headings = $indexes['headings'] ?? [];
        foreach ($headings as $heading) {
            \App\Models\Chapter::create([
                'book_id' => $newBook->id,
                'title' => $heading['title'] ?? '',
                'page_number' => $heading['pg'] ?? 1,
                'level' => $heading['level'] ?? 1,
            ]);
        }
        $this->addLog("✅ تم إنشاء " . count($headings) . " فصل");

        // Handle PDF links
        $this->savePdfLink($newBook, $meta);

        // Store book ID for page import
        $this->currentBookData['book_id'] = $newBook->id;
    }

    /**
     * Save PDF download link
     */
    protected function savePdfLink(Book $book, array $meta)
    {
        if (!isset($meta['pdf_links']) || empty($meta['pdf_links'])) {
            return;
        }

        $pdfLinks = $meta['pdf_links'];
        $root = $pdfLinks['root'] ?? null;
        $files = $pdfLinks['files'] ?? [];

        if (empty($root) || empty($files)) {
            return;
        }

        $pdfFile = $files[0];
        $pdfUrl = 'https://files.turath.io/pdf/' . rawurlencode($root) . '/' . rawurlencode($pdfFile);

        $bookMetadata = \App\Models\BookMetadata::firstOrNew(['book_id' => $book->id]);
        $downloadLinks = $bookMetadata->download_links ?? [];
        $downloadLinks[] = [
            'url' => $pdfUrl,
            'platform' => 'other',
            'type' => 'pdf',
            'notes' => 'صور المطبوع من موقع تراث',
        ];

        $bookMetadata->download_links = $downloadLinks;
        $bookMetadata->pdf_root = $root;
        $bookMetadata->save();

        $this->addLog("📄 تم حفظ رابط PDF: {$pdfFile}");
    }

    /**
     * Finish importing current book
     */
    protected function finishCurrentBook(array &$book)
    {
        $book['status'] = 'done';
        $book['message'] = $this->forceReimport ? 'تم التحديث' : 'تم الاستيراد';

        $totalPages = $this->currentBookData['total_pages'] ?? 0;
        $this->addLog("═══════════════════════════════════════");
        $this->addLog("✅ تم الاستيراد بنجاح!");
        $this->addLog("📄 إجمالي الصفحات: {$totalPages}");

        $this->completedBooks++;
        $this->isImportingCurrentBook = false;
        $this->currentBookImportState = null;
        $this->currentBookData = [];
        $this->currentBookPageOffset = 0;
        $this->currentBookIndex++;
        $this->updateProgress();
    }

    /**
     * Update progress percentage
     */
    protected function updateProgress()
    {
        if (count($this->books) > 0) {
            $this->progress = round(($this->currentBookIndex / count($this->books)) * 100);
        }
    }

    /**
     * Finish import process
     */
    protected function finishImport()
    {
        $this->isImporting = false;
        $this->addLog("🎉 اكتملت عملية الاستيراد!");
        $this->addLog("📊 النتيجة: {$this->completedBooks} نجاح، {$this->failedBooks} فشل");
        $this->statusMessage = "اكتمل الاستيراد: {$this->completedBooks} نجاح، {$this->failedBooks} فشل";
    }

    /**
     * Stop import
     */
    public function stopImport()
    {
        $this->isImporting = false;
        $this->isImportingCurrentBook = false;
        $this->addLog("⏸️ تم إيقاف الاستيراد");
        $this->statusMessage = "تم إيقاف الاستيراد عند الكتاب رقم " . ($this->currentBookIndex + 1);
    }

    /**
     * Reset form
     */
    public function resetForm()
    {
        $this->reset([
            'categoryUrl',
            'manualBookIds',
            'books',
            'currentBookIndex',
            'isImporting',
            'isFetching',
            'progress',
            'completedBooks',
            'failedBooks',
            'importLog',
            'statusMessage',
            'isImportingCurrentBook',
            'currentBookImportState',
            'currentBookPageOffset',
            'currentBookData',
        ]);
    }

    /**
     * Add log entry
     */
    protected function addLog(string $message): void
    {
        $time = now()->format('H:i:s');
        $this->importLog[] = "[{$time}] {$message}";
    }
}
