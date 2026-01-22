<?php

namespace App\Livewire;

use App\Models\Author;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Page;
use App\Models\Volume;
use App\Services\KetabOnlineScraperService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ImportKetabOnlinePage extends Component
{
    public $bookId = '';
    public $logs = [];
    public $processedPages = 0;
    public $totalPages = 0;
    public $isImporting = false;
    public $currentBookTitle = '';
    public $readyToLoadPages = false;
    public $totalVolumes = 0;

    // Internal state
    protected $scraper;
    public $bookData = [];
    public $batchSize = 20; // Number of pages to fetch in parallel
    public $currentBatchStart = 1;

    public function boot(KetabOnlineScraperService $scraper)
    {
        $this->scraper = $scraper;
    }

    public function render()
    {
        return view('livewire.import-ketab-online-page')
            ->layout('components.layouts.app');
    }

    public function startImport()
    {
        $this->validate([
            'bookId' => 'required',
        ]);

        // Extract ID from URL if full URL is pasted
        // Example: https://ketabonline.com/ar/books/3501 -> 3501
        if (preg_match('/books\/(\d+)/', $this->bookId, $matches)) {
            $this->bookId = $matches[1];
        }

        $this->isImporting = true;
        $this->logs = [];
        $this->processedPages = 0;
        $this->addLog("🚀 بدء الاتصال بـ KetabOnline للكتاب ID: {$this->bookId}...", 'info');

        try {
            // 1. Fetch Metadata & Structure
            $this->bookData = $this->scraper->fetchBookInfo($this->bookId);
            $this->currentBookTitle = $this->bookData['title'];
            $this->totalVolumes = $this->bookData['total_volumes'];

            $this->addLog("✅ تم العثور على الكتاب: {$this->bookData['title']}", 'success');
            $this->addLog("👤 والمؤلف: {$this->bookData['author']['name']}", 'info');
            $this->addLog("📚 عدد المجلدات: {$this->totalVolumes}", 'info');
            $this->addLog("📑 عدد الفصول: " . count($this->bookData['chapters']), 'info');

            // 2. Setup Database Records
            $this->setupBookStructure();

            // 3. Prepare for Batch Import
            // We use the last chapter's start page as a rough estimate for total pages, 
            // but we will keep fetching until we hit 404s or empty content.
            $this->totalPages = $this->bookData['total_pages_estimate'];
            $this->currentBatchStart = 1;
            
            // Trigger the page loading loop
            $this->readyToLoadPages = true;

        } catch (\Exception $e) {
            $this->addLog("❌ خطأ فادح: " . $e->getMessage(), 'error');
            $this->isImporting = false;
        }
    }

    public function importBatch()
    {
        if (!$this->isImporting || !$this->readyToLoadPages) return;

        $start = $this->currentBatchStart;
        $end = $start + $this->batchSize - 1;

        $this->addLog("Rx جاري سحب الصفحات {$start} إلى {$end}...", 'info');

        try {
            // Fetch pages in parallel
            $pagesContent = $this->scraper->fetchPagesParallel(
                $this->bookId, 
                $start, 
                $end, 
                $this->bookData['chapters'] // Pass chapters to map volumes
            );

            if (empty($pagesContent)) {
                $this->addLog("⚠️ لم يتم العثور على محتوى في هذه الدفعة. ربما انتهى الكتاب.", 'warning');
                // Could be end of book. Let's try one more batch to be sure or stop.
                // For safety, if starting page is empty, we likely finished.
                if (empty($pagesContent)) {
                     $this->finishImport();
                     return;
                }
            }

            // Save Pages to DB
            DB::beginTransaction();
            foreach ($pagesContent as $pageNum => $text) {
                Page::updateOrCreate(
                    [
                        'book_id' => $this->currentBookId, // Set in setupBookStructure
                        'page_number' => $pageNum,
                    ],
                    [
                        'content' => $text,
                        'volume_id' => $this->getSimpleVolumeId($pageNum) // Helper
                    ]
                );
            }
            DB::commit();

            $count = count($pagesContent);
            $this->processedPages += $count;
            $this->addLog("✅ تم تخزين {$count} صفحة بنجاح.", 'success');

            // Move to next batch
            $this->currentBatchStart += $this->batchSize;

            // Safety break: if we fetched fewer pages than requested, maybe we hit the end
            if ($count < $this->batchSize) {
                 // But wait, parallel requests might fail individually. 
                 // Let's assume valid end only if count is 0, handled above.
            }
            
            // Hard limit safety
            if ($this->currentBatchStart > 50000) { 
                $this->finishImport(); 
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addLog("⚠️ خطأ في الدفعة: " . $e->getMessage(), 'error');
            // Continue usually, or retry? let's continue.
            $this->currentBatchStart += $this->batchSize;
        }
    }

    public $currentBookId;
    public $createdVolumes = [];

    protected function setupBookStructure()
    {
        DB::beginTransaction();
        try {
            // 1. Create/Update Author
            $authorName = $this->bookData['author']['name'] ?? 'مجهول';
            $author = Author::firstOrCreate(['name' => $authorName]);

            // 2. Create Book
            $book = Book::updateOrCreate(
                ['shamela_id' => 9000000 + (int)$this->bookId], // Offset ID to avoid collision with Turath
                [
                    'title' => $this->bookData['title'],
                    'other_data' => [
                        'source' => 'ketabonline',
                        'original_id' => $this->bookId,
                        'publisher' => $this->bookData['publisher'] ?? null,
                        'description' => $this->bookData['description'] ?? null,
                    ]
                ]
            );
            $this->currentBookId = $book->id;

            // Link Author
            if (!$book->authors()->where('author_id', $author->id)->exists()) {
                $book->authors()->attach($author->id);
            }

            // 3. Create Volumes
            // We know total volumes from metadata
            $this->createdVolumes = [];
            for ($i = 1; $i <= max(1, $this->bookData['total_volumes']); $i++) {
                $vol = Volume::updateOrCreate(
                    ['book_id' => $book->id, 'volume_number' => $i],
                    ['name' => "المجلد {$i}"]
                );
                $this->createdVolumes[$i] = $vol->id;
            }

            // 4. Create Chapters (Simple Flat List for now, or Nested if level exists)
            // Cleanup old chapters for re-import
            Chapter::where('book_id', $book->id)->delete();
            
            foreach ($this->bookData['chapters'] as $chap) {
                Chapter::create([
                    'book_id' => $book->id,
                    'title' => mb_substr($chap['title'], 0, 250),
                    'page_number' => $chap['start_page'],
                    'level' => $chap['level'] ?? 1,
                    // 'volume_id' => ... ideally link to volume
                ]);
            }

            DB::commit();
            $this->addLog("🗄️ تم تجهيز قاعدة البيانات (الكتاب، المجلدات، الفصول).", 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function getSimpleVolumeId($pageNum)
    {
        // Find volume from chapters map in bookData
        // Need to pass bookData between requests (state). 
        // Livewire dehydrate/hydrate handles public props, 
        // but large arrays might be heavy. Ideally query DB chapters.
        // For simplicity in this v1, we default to volume 1 or use local logic.
        return $this->createdVolumes[1] ?? null; 
    }

    protected function finishImport()
    {
        $this->isImporting = false;
        $this->readyToLoadPages = false;
        $this->addLog("🏁 تم الانتهاء من الاستيراد بنجاح!", 'success');
        $this->addLog("🎉 إجمالي الصفحات: {$this->processedPages}", 'success');
    }

    protected function addLog($message, $type = 'info')
    {
        $this->logs[] = [
            'time' => now()->format('H:i:s'),
            'message' => $message,
            'type' => $type
        ];
    }
}
