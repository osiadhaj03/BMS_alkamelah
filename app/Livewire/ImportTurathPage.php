<?php

namespace App\Livewire;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookMetadata;
use App\Models\BookSection;
use App\Models\BookSource;
use App\Models\Chapter;
use App\Models\Page;
use App\Models\Volume;
use App\Services\MetadataParserService;
use App\Services\TurathScraperService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('استيراد كتاب من تراث')]
class ImportTurathPage extends Component
{
    // حالة الاستيراد
    public bool $isImporting = false;
    public int $progress = 0;
    public int $totalPages = 0;
    public int $importedPages = 0;
    public string $statusMessage = '';
    public array $importLog = [];

    // Chunking State
    public ?int $currentBookId = null;
    public ?int $turathBookId = null;
    public ?array $volumeIdMap = null; // Map volume_number => volume_id
    public ?array $pageMap = null;
    public int $batchSize = 100; // عدد الصفحات في كل دفعة (مرفع للسرعة)

    // بيانات النموذج
    public string $bookUrl = '';
    public bool $skipPages = false;
    public bool $forceReimport = false;

    // بيانات الكتاب المستخرجة
    public ?array $bookInfo = null;
    public ?array $parsedInfo = null;

    // Selected Options
    public ?int $sectionId = null;
    public $sections = [];

    public function mount()
    {
        $this->sections = \App\Models\BookSection::pluck('name', 'id')->toArray();
    }

    protected $rules = [
        'bookUrl' => 'required',
    ];

    public function render()
    {
        return view('livewire.import-turath-page')
            ->layout('components.layouts.app');
    }

    protected function extractBookId(string $input): ?int
    {
        $input = trim($input);
        if (is_numeric($input))
            return (int) $input;
        if (preg_match('/book\/(\d+)/i', $input, $matches))
            return (int) $matches[1];
        return null;
    }

    public function previewBook()
    {
        $this->validate(['bookUrl' => 'required']);
        $bookId = $this->extractBookId($this->bookUrl);

        if (!$bookId) {
            $this->addError('bookUrl', 'الرجاء إدخال رابط أو معرف صحيح');
            return;
        }

        $this->statusMessage = 'جاري جلب معلومات الكتاب...';
        $this->addLog('📡 جاري جلب معلومات الكتاب...');

        $scraper = app(TurathScraperService::class);
        $parser = app(MetadataParserService::class);

        $this->bookInfo = $scraper->getBookInfo($bookId);

        if (!$this->bookInfo || !isset($this->bookInfo['meta'])) {
            $this->addError('bookUrl', 'فشل جلب معلومات الكتاب. تأكد من صحة المعرف.');
            $this->statusMessage = '';
            return;
        }

        $this->parsedInfo = $parser->parseBookInfo($this->bookInfo['meta']['info'] ?? '');
        $volumeBounds = $this->bookInfo['indexes']['volume_bounds'] ?? [];
        $this->totalPages = $scraper->getTotalPages($volumeBounds);

        $this->statusMessage = '';
        $this->addLog("✅ تم جلب معلومات الكتاب: {$this->bookInfo['meta']['name']}");
    }

    public function startImport()
    {
        $this->validate(['bookUrl' => 'required']);
        $turathId = $this->extractBookId($this->bookUrl);

        if (!$turathId) {
            $this->addError('bookUrl', 'الرجاء إدخال رابط صحيح');
            return;
        }

        // تحقق مبدئي
        $existingBook = Book::where('shamela_id', $turathId)->first();
        if ($existingBook && !$this->forceReimport) {
            $this->addLog("⚠️ الكتاب موجود مسبقاً: {$existingBook->title}");
            $this->statusMessage = "الكتاب موجود مسبقاً: {$existingBook->title}";
            return;
        }

        // Reset State
        $this->isImporting = true;
        $this->progress = 0;
        $this->importedPages = 0;
        $this->importLog = [];
        $this->turathBookId = $turathId;

        $this->addLog('🚀 بدء عملية الاستيراد...');

        // 1. Setup Phase (Metadata, DB Records)
        try {
            $this->setupBookStructure($turathId, $existingBook);
        } catch (\Exception $e) {
            $this->isImporting = false;
            $this->addLog("❌ خطأ في التهيئة: {$e->getMessage()}");
            $this->statusMessage = "فشل: {$e->getMessage()}";
        }
    }

    protected function setupBookStructure(int $turathId, ?Book $existingBook)
    {
        $scraper = app(TurathScraperService::class);
        $parser = app(MetadataParserService::class);

        // Fetch info if not previewed
        if (!$this->bookInfo) {
            $this->addLog('📡 جاري جلب معلومات الكتاب...');
            $this->bookInfo = $scraper->getBookInfo($turathId);
        }

        if (!$this->bookInfo)
            throw new \Exception("لم يتم العثور على الكتاب");

        $meta = $this->bookInfo['meta'];
        $indexes = $this->bookInfo['indexes'] ?? [];
        $this->pageMap = $indexes['page_map'] ?? null;

        $parsedInfo = $parser->parseBookInfo($meta['info'] ?? '');
        $authorData = $parser->extractAuthorDates($parsedInfo['author_name'] ?? '');

        // Determine correct total pages
        // Prioritize page_map if available for accuracy
        if ($this->pageMap) {
            $this->totalPages = count($this->pageMap);
        } else {
            $this->totalPages = $scraper->getTotalPages($indexes['volume_bounds'] ?? []);
        }

        $volumes = $scraper->parseVolumes($indexes['volume_bounds'] ?? []);
        $chapters = $scraper->parseChapters($indexes['headings'] ?? []);

        $this->addLog("📖 اسم الكتاب: {$meta['name']}");
        $this->addLog("📚 المجلدات: " . count($volumes));
        $this->addLog("📄 الصفحات: {$this->totalPages}");

        DB::transaction(function () use ($meta, $parsedInfo, $authorData, $volumes, $chapters, $existingBook, $turathId, $parser) {
            // Delete old
            if ($existingBook && $this->forceReimport) {
                $this->addLog('🗑️ حذف الكتاب القديم...');
                $existingBook->pages()->delete();
                $existingBook->chapters()->delete();
                $existingBook->volumes()->delete();
                $existingBook->authors()->detach();
                $existingBook->delete();
            }

            // Create Author
            $author = $this->findOrCreateAuthor($authorData, $parsedInfo);

            // Create Book
            $book = $this->createBook($turathId, $meta, $parser);
            $this->currentBookId = $book->id;

            // Attach Author
            if ($author) {
                $book->authors()->attach($author->id, ['role' => 'author', 'is_main' => true, 'display_order' => 1]);
                $this->addLog("✅ المؤلف: {$author->full_name}");
            }

            // Create Editor (Fix Missing Editor Logic)
            if (!empty($parsedInfo['editor_name'])) {
                $editorData = $parser->extractAuthorDates($parsedInfo['editor_name']);
                $editor = $this->findOrCreateAuthor($editorData, ['author_name' => $parsedInfo['editor_name']]);
                if ($editor) {
                    $book->authors()->attach($editor->id, ['role' => 'editor', 'is_main' => false, 'display_order' => 2]);
                    $this->addLog("✅ المحقق: {$editor->full_name}");
                }
            }

            // Create Volumes
            $volumeModels = $this->createVolumes($book, $volumes);
            // Cache volume IDs for importing pages
            $this->volumeIdMap = collect($volumeModels)->mapWithKeys(fn($v) => [$v->number => $v->id])->toArray();

            $this->addLog("✅ تم إنشاء " . count($volumeModels) . " مجلد");

            // Create Chapters
            $this->createChapters($book, $chapters, $volumeModels);
            $this->addLog("✅ تم إنشاء " . count($chapters) . " فصل");

            // Save PDF Link if available
            $this->savePdfLink($book, $meta);
        });

        // If skip pages, finish immediately
        if ($this->skipPages || $this->totalPages == 0) {
            $this->finishImport();
        } else {
            $this->addLog("📄 جاري استيراد الصفحات...");
        }
    }

    public function importBatch()
    {
        if (!$this->isImporting || !$this->currentBookId)
            return;

        $scraper = app(TurathScraperService::class);
        $totalReal = ($this->pageMap) ? count($this->pageMap) : $this->totalPages;

        $startIdx = $this->importedPages; // 0-based index of imported count
        $endIdx = min($startIdx + $this->batchSize, $totalReal);

        if ($startIdx >= $totalReal) {
            $this->finishImport();
            return;
        }

        // Calculate pages to fetch in this batch
        $pagesList = [];
        // Note: For Turath API, if no pageMap, we assume pages are 1..N
        // If pageMap exists, we don't really use it for *fetching* by ID usually, 
        // but getAllPages was iterating. 
        // Let's stick to simple 1-based page numbers for the API call 
        // since the API `pg` param usually corresponds to the sequential index.
        for ($i = $startIdx + 1; $i <= $endIdx; $i++) {
            $pagesList[] = $i;
        }

        $pages = [];
        $batchCount = 0;

        try {
            // Use Parallel Parallel
            $fetchedPages = $scraper->fetchPagesParallel($this->turathBookId, $pagesList, 20); // concurrency 20

            foreach ($fetchedPages as $pageData) {
                $volNum = $pageData['volume_number'];
                $volId = $this->volumeIdMap[$volNum] ?? reset($this->volumeIdMap);

                $pages[] = [
                    'book_id' => $this->currentBookId,
                    'volume_id' => $volId,
                    'page_number' => $pageData['page_number'],
                    'original_page_number' => $pageData['original_page_number'],
                    'content' => $pageData['content'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $batchCount++;
            }

            if (!empty($pages)) {
                Page::insert($pages);
                $this->importedPages += count($pagesList); // Advance even if some failed to avoid loop? 
                // Better: trust batch count but assume sequential progress
            } else {
                // Fail safe backup
                $this->importedPages += count($pagesList);
            }

            // Update Progress
            if ($totalReal > 0) {
                $this->progress = (int) (($this->importedPages / $totalReal) * 100);
            }

            $startShow = $startIdx + 1;
            $endShow = $startIdx + $batchCount;
            $this->addLog("✅ تم استيراد دفعة {$startShow}-{$endShow}");

            // Check completion
            if ($this->importedPages >= $totalReal) {
                $this->finishImport();
            }

        } catch (\Exception $e) {
            $this->addLog("❌ خطأ في الدفعة: {$e->getMessage()}");
            $this->importedPages += $this->batchSize; // Skip bad batch
            if ($this->importedPages >= $totalReal) {
                $this->finishImport();
            }
        }
    }

    protected function finishImport()
    {
        $this->isImporting = false;
        $this->progress = 100;
        $this->addLog('═══════════════════════════════════════');
        $this->addLog('✅ تم الاستيراد بنجاح!');
        $this->addLog("📄 إجمالي الصفحات: {$this->importedPages}");
        $this->statusMessage = "تم الاستيراد بنجاح!";

        // Cleanup sensitive/large data
        $this->pageMap = null;
        $this->bookInfo = null;
    }

    protected function findOrCreateAuthor(array $authorData, array $parsedInfo): ?Author
    {
        $fullName = $authorData['clean_name'] ?? $parsedInfo['author_name'] ?? null;
        if (empty($fullName))
            return null;

        return Author::firstOrCreate(
            ['full_name' => $fullName],
            [
                'is_living' => false,
                'birth_date' => $authorData['birth_year'] ? "{$authorData['birth_year']}-01-01" : null,
                'death_date' => $authorData['death_year'] ? "{$authorData['death_year']}-01-01" : null,
            ]
        );
    }

    protected function createBook(int $turathId, array $meta, MetadataParserService $parser): Book
    {
        $title = $parser->cleanBookName($meta['name']);

        // 1. التعامل مع المصدر (تراث)
        $source = BookSource::firstOrCreate(
            ['name' => 'موقع تراث (Turath.io)'],
            ['name' => 'موقع تراث (Turath.io)']
        );

        // 2. محاولة تحديد القسم
        // إذا اختار المستخدم قسماً، نستخدمه. وإلا نتركه فارغاً.
        $finalSectionId = $this->sectionId;

        // إذا لم يختر، نحاول البحث (اختياري)
        if (!$finalSectionId && isset($meta['cat_id'])) {
            // يمكن هنا إضافة منطق
        }

        // 3. تجهيز الرابط الكامل للمصدر
        $fullUrl = $this->bookUrl;
        if (is_numeric($fullUrl)) {
            $fullUrl = "https://app.turath.io/book/{$fullUrl}";
        }

        return Book::create([
            'shamela_id' => (string) $turathId,
            'title' => $title,
            'description' => $meta['info'] ?? null,
            'visibility' => 'public',
            'has_original_pagination' => true,
            'book_source_id' => $source->id,
            'additional_notes' => "رابط المصدر: {$fullUrl}", // حفظ الرابط الكامل
            'book_section_id' => $finalSectionId,
        ]);
    }

    protected function createVolumes(Book $book, array $volumes): array
    {
        $volumeModels = [];
        if (empty($volumes)) {
            $volumeModels[1] = Volume::create(['book_id' => $book->id, 'number' => 1]);
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

    protected function createChapters(Book $book, array $chapters, array $volumeModels): void
    {
        foreach ($chapters as $chapterData) {
            $volumeId = null;
            if ($chapterData['page_start']) {
                foreach ($volumeModels as $volume) {
                    if ($volume->page_start && $volume->page_end) {
                        if ($chapterData['page_start'] >= $volume->page_start && $chapterData['page_start'] <= $volume->page_end) {
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
     * Save PDF link if available in book metadata from Turath API
     */
    protected function savePdfLink(Book $book, array $meta): void
    {
        // Check if pdf_links exists in meta
        if (!isset($meta['pdf_links']) || empty($meta['pdf_links'])) {
            return;
        }

        $pdfLinks = $meta['pdf_links'];
        $root = $pdfLinks['root'] ?? null;
        $files = $pdfLinks['files'] ?? [];

        if (empty($root) || empty($files)) {
            return;
        }

        // Build the PDF URL: https://files.turath.io/pdf/{root}/{file}
        $pdfFile = $files[0]; // Usually only one PDF file
        $pdfUrl = 'https://files.turath.io/pdf/' . rawurlencode($root) . '/' . rawurlencode($pdfFile);

        // Create or update BookMetadata with download_links
        $bookMetadata = BookMetadata::firstOrNew(['book_id' => $book->id]);

        $downloadLinks = $bookMetadata->download_links ?? [];
        $downloadLinks[] = [
            'url' => $pdfUrl,
            'platform' => 'other',
            'type' => 'pdf',
            'notes' => 'صور المطبوع من موقع تراث - Turath.io',
        ];

        $bookMetadata->download_links = $downloadLinks;
        $bookMetadata->save();

        $this->addLog('📄 تم حفظ رابط PDF: ' . $pdfFile);
    }

    protected function addLog(string $message): void
    {
        $time = now()->format('H:i:s');
        $this->importLog[] = "[$time] $message";
    }

    public function resetForm()
    {
        $this->reset(['bookUrl', 'skipPages', 'forceReimport', 'bookInfo', 'parsedInfo', 'progress', 'importedPages', 'totalPages', 'importLog', 'statusMessage']);
    }
}
