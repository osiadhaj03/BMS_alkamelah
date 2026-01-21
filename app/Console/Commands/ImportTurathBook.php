<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookSection;
use App\Models\Chapter;
use App\Models\Page;
use App\Models\Publisher;
use App\Models\Volume;
use App\Services\MetadataParserService;
use App\Services\TurathScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * أمر استيراد كتاب من Turath.io
 * 
 * الاستخدام:
 * php artisan turath:import {book_id}
 * php artisan turath:import {book_id} --dry-run
 * php artisan turath:import {book_id} --skip-pages
 */
class ImportTurathBook extends Command
{
    /**
     * اسم الأمر ووصفه
     */
    protected $signature = 'turath:import 
                            {book_id : معرف الكتاب في Turath.io}
                            {--dry-run : معاينة بدون حفظ}
                            {--skip-pages : استيراد بدون الصفحات}
                            {--force : إعادة الاستيراد حتى لو كان موجوداً}
                            {--delay=500 : التأخير بين الطلبات بالميلي ثانية}';

    protected $description = 'استيراد كتاب من موقع Turath.io إلى قاعدة البيانات';

    /**
     * الخدمات
     */
    protected TurathScraperService $scraper;
    protected MetadataParserService $parser;

    /**
     * إحصائيات الاستيراد
     */
    protected array $stats = [
        'pages_imported' => 0,
        'chapters_imported' => 0,
        'volumes_imported' => 0,
    ];

    public function __construct(
        TurathScraperService $scraper,
        MetadataParserService $parser
    ) {
        parent::__construct();
        $this->scraper = $scraper;
        $this->parser = $parser;
    }

    /**
     * تنفيذ الأمر
     */
    public function handle(): int
    {
        $bookId = (int) $this->argument('book_id');
        $isDryRun = $this->option('dry-run');
        $skipPages = $this->option('skip-pages');
        $force = $this->option('force');
        $delay = (int) $this->option('delay');

        set_time_limit(0);
        $this->scraper->setDelay($delay);

        $this->printHeader($bookId);

        // ============================================
        // 1. التحقق من وجود الكتاب مسبقاً
        // ============================================
        $existingBook = Book::where('shamela_id', $bookId)->first();
        if ($existingBook && !$force) {
            $this->error("❌ الكتاب موجود مسبقاً: {$existingBook->title}");
            $this->info("   استخدم --force لإعادة الاستيراد");
            return Command::FAILURE;
        }

        // ============================================
        // 2. جلب معلومات الكتاب
        // ============================================
        $this->info('📡 جاري جلب معلومات الكتاب...');

        $bookInfo = $this->scraper->getBookInfo($bookId);

        if (!$bookInfo || !isset($bookInfo['meta'])) {
            $this->error('❌ فشل جلب معلومات الكتاب');
            return Command::FAILURE;
        }

        $meta = $bookInfo['meta'];
        $indexes = $bookInfo['indexes'] ?? [];

        $this->info("✅ تم جلب معلومات الكتاب: {$meta['name']}");

        // ============================================
        // 3. تحليل البيانات الوصفية
        // ============================================
        $this->info('🔍 جاري تحليل البيانات الوصفية...');

        $parsedInfo = $this->parser->parseBookInfo($meta['info'] ?? '');
        $authorData = $this->parser->extractAuthorDates($parsedInfo['author_name'] ?? '');

        $this->displayParsedInfo($meta, $parsedInfo, $authorData);

        // ============================================
        // 4. تحليل المجلدات والفصول
        // ============================================
        $volumeBounds = $indexes['volume_bounds'] ?? [];
        $headings = $indexes['headings'] ?? [];

        $volumes = $this->scraper->parseVolumes($volumeBounds);
        $chapters = $this->scraper->parseChapters($headings);
        $totalPages = $this->scraper->getTotalPages($volumeBounds);

        $this->info("📚 المجلدات: " . count($volumes));
        $this->info("📑 الفصول: " . count($chapters));
        $this->info("📄 الصفحات: {$totalPages}");

        // ============================================
        // 5. وضع المعاينة (Dry Run)
        // ============================================
        if ($isDryRun) {
            $this->warn("\n⚠️ وضع المعاينة - لن يتم حفظ أي بيانات");
            $this->displayPreview($meta, $parsedInfo, $volumes, $chapters);
            return Command::SUCCESS;
        }

        // ============================================
        // 6. بدء الاستيراد
        // ============================================
        $this->newLine();
        $this->info('💾 جاري الاستيراد...');

        try {
            DB::transaction(function () use ($bookId, $meta, $parsedInfo, $authorData, $volumes, $chapters, $totalPages, $skipPages, $existingBook, $force) {
                // حذف الكتاب القديم إذا كان موجوداً و force = true
                if ($existingBook && $force) {
                    $this->warn("🗑️ حذف الكتاب القديم...");
                    $existingBook->pages()->delete();
                    $existingBook->chapters()->delete();
                    $existingBook->volumes()->delete();
                    $existingBook->authors()->detach();
                    $existingBook->delete();
                }

                // 6.1 إنشاء/جلب المؤلف
                $author = $this->findOrCreateAuthor($authorData, $parsedInfo);

                // 6.2 إنشاء/جلب المحقق (إذا وجد)
                $editor = null;
                if (!empty($parsedInfo['editor_name'])) {
                    $editorData = $this->parser->extractAuthorDates($parsedInfo['editor_name']);
                    $editor = $this->findOrCreateAuthor($editorData, ['author_name' => $parsedInfo['editor_name']]);
                }

                // 6.3 إنشاء الكتاب
                $book = $this->createBook($bookId, $meta);

                // 6.4 ربط المؤلف
                if ($author) {
                    $book->authors()->attach($author->id, [
                        'role' => 'author',
                        'is_main' => true,
                        'display_order' => 1,
                    ]);
                    $this->info("✅ تم ربط المؤلف: {$author->full_name}");
                }

                // 6.5 ربط المحقق
                if ($editor) {
                    $book->authors()->attach($editor->id, [
                        'role' => 'editor',
                        'is_main' => false,
                        'display_order' => 2,
                    ]);
                    $this->info("✅ تم ربط المحقق: {$editor->full_name}");
                }

                // 6.6 إنشاء المجلدات
                $volumeModels = $this->createVolumes($book, $volumes);

                // 6.7 إنشاء الفصول
                $this->createChapters($book, $chapters, $volumeModels);

                // 6.8 استيراد الصفحات
                if (!$skipPages && $totalPages > 0) {
                    $this->importPages($book, $bookId, $totalPages, $volumeModels);
                }
            });

            $this->printSuccess($meta['name']);
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ فشل الاستيراد: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
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
     * إنشاء سجل الكتاب
     */
    protected function createBook(int $turathId, array $meta): Book
    {
        $title = $this->parser->cleanBookName($meta['name']);

        $book = Book::create([
            'shamela_id' => (string) $turathId,
            'title' => $title,
            'description' => $meta['info'] ?? null,
            'visibility' => 'public',
            'has_original_pagination' => true,
        ]);

        $this->info("✅ تم إنشاء الكتاب: {$book->title}");
        return $book;
    }

    /**
     * إنشاء المجلدات
     */
    protected function createVolumes(Book $book, array $volumes): array
    {
        $volumeModels = [];

        if (empty($volumes)) {
            // إنشاء مجلد افتراضي
            $volumeModels[1] = Volume::create([
                'book_id' => $book->id,
                'number' => 1,
                'title' => null,
            ]);
            $this->stats['volumes_imported'] = 1;
        } else {
            foreach ($volumes as $volumeData) {
                $volume = Volume::create([
                    'book_id' => $book->id,
                    'number' => $volumeData['number'],
                    'title' => null,
                    'page_start' => $volumeData['page_start'],
                    'page_end' => $volumeData['page_end'],
                ]);
                $volumeModels[$volumeData['number']] = $volume;
            }
            $this->stats['volumes_imported'] = count($volumes);
        }

        $this->info("✅ تم إنشاء {$this->stats['volumes_imported']} مجلد");
        return $volumeModels;
    }

    /**
     * إنشاء الفصول (مع دعم التداخل)
     */
    protected function createChapters(Book $book, array $chapters, array $volumeModels): void
    {
        $lastChapterByLevel = [];

        foreach ($chapters as $chapterData) {
            $level = $chapterData['level'] ?? 1;

            // تحديد المجلد حسب رقم الصفحة
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

            // استخدام أول مجلد إذا لم يتم تحديده
            $volumeId = $volumeId ?? reset($volumeModels)?->id;

            // تحديد الأب بناءً على المستوى
            $parentId = null;
            if ($level > 1) {
                // الأب هو آخر فصل تم إنشاؤه في المستوى السابق مباشرة
                $parentId = $lastChapterByLevel[$level - 1] ?? null;
            }

            $chapter = Chapter::create([
                'book_id' => $book->id,
                'volume_id' => $volumeId,
                'parent_id' => $parentId,
                'title' => mb_substr($chapterData['title'], 0, 250),
                'level' => $level,
                'order' => $chapterData['order'],
                'page_start' => $chapterData['page_start'],
            ]);

            // حفظ معرف هذا الفصل لهذا المستوى لاستخدامه كأب للفصول القادمة في المستوى الأدنى
            $lastChapterByLevel[$level] = $chapter->id;

            // عند إنشاء فصل في مستوى معين، يجب مسح أي فصول "باقية" من مستويات أعمق
            // لضمان عدم ربط فصول جديدة بأباء قدامى من أفرع أخرى
            foreach ($lastChapterByLevel as $l => $id) {
                if ($l > $level) {
                    unset($lastChapterByLevel[$l]);
                }
            }
        }

        $this->stats['chapters_imported'] = count($chapters);
        $this->info("✅ تم إنشاء {$this->stats['chapters_imported']} فصل (هيكل شجري)");
    }

    /**
     * استيراد الصفحات (جلب متوازي)
     */
    protected function importPages(Book $book, int $turathBookId, int $totalPages, array $volumeModels): void
    {
        $this->newLine();
        $this->info("📄 جاري استيراد الصفحات ({$totalPages} صفحة)...");

        $batchSize = 100; // جلب 100 صفحة معاً (10 طلبات متوازية × 10 = 100)
        $pageNumbers = range(1, $totalPages);
        $chunks = array_chunk($pageNumbers, $batchSize);
        $batchIndex = 0;

        foreach ($chunks as $chunk) {
            $batchStart = $chunk[0];
            $batchEnd = end($chunk);

            // جلب الصفحات بشكل متوازي (10 طلبات في نفس الوقت)
            $fetchedPages = $this->scraper->fetchPagesParallel($turathBookId, $chunk, 10);

            // تجهيز البيانات للإدخال
            $insertData = [];
            foreach ($fetchedPages as $pageNum => $pageData) {
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

                $insertData[] = [
                    'book_id' => $book->id,
                    'volume_id' => $volumeId,
                    'page_number' => $pageData['page_number'],
                    'original_page_number' => $pageData['original_page_number'] ?? $pageData['page_number'],
                    'content' => $pageData['content'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // إدخال الدفعة
            if (!empty($insertData)) {
                Page::insert($insertData);
                $this->stats['pages_imported'] += count($insertData);

                $timestamp = now()->format('H:i:s');
                $this->line("[{$timestamp}] ✅ تم استيراد دفعة {$batchStart}-{$batchEnd}");
            }

            $batchIndex++;
        }

        $this->newLine();
        $this->info("✅ تم استيراد {$this->stats['pages_imported']} صفحة");
    }

    /**
     * طباعة الترويسة
     */
    protected function printHeader(int $bookId): void
    {
        $this->newLine();
        $this->line('═══════════════════════════════════════════════════════');
        $this->info('  🚀 استيراد كتاب من Turath.io');
        $this->line('═══════════════════════════════════════════════════════');
        $this->info("  📚 معرف الكتاب: {$bookId}");
        $this->line('═══════════════════════════════════════════════════════');
        $this->newLine();
    }

    /**
     * عرض المعلومات المستخرجة
     */
    protected function displayParsedInfo(array $meta, array $parsedInfo, array $authorData): void
    {
        $this->newLine();
        $this->line('┌─────────────────────────────────────────────────────┐');
        $this->line('│ 📖 معلومات الكتاب                                  │');
        $this->line('├─────────────────────────────────────────────────────┤');
        $this->line("│ الاسم: " . Str::limit($meta['name'], 40));

        if ($parsedInfo['author_name']) {
            $this->line("│ المؤلف: " . Str::limit($parsedInfo['author_name'], 38));
        }
        if ($authorData['death_year']) {
            $this->line("│ سنة الوفاة: {$authorData['death_year']} هـ");
        }
        if ($parsedInfo['editor_name']) {
            $this->line("│ المحقق: " . Str::limit($parsedInfo['editor_name'], 38));
        }

        $this->line('└─────────────────────────────────────────────────────┘');
    }

    /**
     * عرض المعاينة
     */
    protected function displayPreview(array $meta, array $parsedInfo, array $volumes, array $chapters): void
    {
        $this->newLine();
        $this->table(
            ['العنصر', 'القيمة'],
            [
                ['اسم الكتاب', Str::limit($meta['name'], 50)],
                ['المؤلف', $parsedInfo['author_name'] ?? '-'],
                ['المحقق', $parsedInfo['editor_name'] ?? '-'],
                ['المجلدات', count($volumes)],
                ['الفصول', count($chapters)],
            ]
        );

        if (!empty($chapters)) {
            $this->newLine();
            $this->info('📑 أول 5 فصول:');
            foreach (array_slice($chapters, 0, 5) as $chapter) {
                $indent = str_repeat('  ', $chapter['level'] - 1);
                $this->line("  {$indent}• {$chapter['title']} (ص{$chapter['page_start']})");
            }
        }
    }

    /**
     * طباعة رسالة النجاح
     */
    protected function printSuccess(string $bookName): void
    {
        $this->newLine();
        $this->line('═══════════════════════════════════════════════════════');
        $this->info('  ✅ تم الاستيراد بنجاح!');
        $this->line('═══════════════════════════════════════════════════════');
        $this->info("  📖 الكتاب: {$bookName}");
        $this->info("  📚 المجلدات: {$this->stats['volumes_imported']}");
        $this->info("  📑 الفصول: {$this->stats['chapters_imported']}");
        $this->info("  📄 الصفحات: {$this->stats['pages_imported']}");
        $this->line('═══════════════════════════════════════════════════════');
        $this->newLine();
    }
}
