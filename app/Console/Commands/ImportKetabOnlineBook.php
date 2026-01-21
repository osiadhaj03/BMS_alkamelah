<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Page;
use App\Models\Volume;
use App\Services\KetabOnlineScraperService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportKetabOnlineBook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ketab:import 
                            {book_id : The ID of the book on KetabOnline}
                            {--force : Force re-import even if exists}
                            {--skip-pages : Import structure only without pages}
                            {--delay=500 : Delay between batches in ms}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a book from KetabOnline.com including metadata, chapters, and pages.';

    protected $scraper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(KetabOnlineScraperService $scraper)
    {
        parent::__construct();
        $this->scraper = $scraper;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bookId = $this->argument('book_id');
        $force = $this->option('force');
        $skipPages = $this->option('skip-pages');
        $delay = (int)$this->option('delay');

        $this->info("🚀 Starting import for Book ID: {$bookId}");

        // Check if exists
        // We use offset 9000000 to differentiate from Turath IDs
        $localId = 9000000 + (int)$bookId;
        if (Book::where('shamela_id', $localId)->exists() && !$force) {
            $this->warn("⚠️ Book already exists. Use --force to overwrite.");
            return 0;
        }

        try {
            // 1. Fetch Info
            $this->info("🔍 Fetching metadata...");
            $bookData = $this->scraper->fetchBookInfo($bookId);

            $this->info("📖 Found: {$bookData['title']}");
            $this->info("👤 Author: {$bookData['author']['name']}");
            $this->info("📑 Stats: {$bookData['total_volumes']} Vols, ~{$bookData['total_pages_estimate']} Pages");

            // 2. Database Setup
            DB::beginTransaction();
            
            // Author
            $author = Author::firstOrCreate(['name' => $bookData['author']['name']]);

            // Book
            $book = Book::updateOrCreate(
                ['shamela_id' => $localId],
                [
                    'title' => $bookData['title'],
                    'other_data' => [
                        'source' => 'ketabonline',
                        'original_id' => $bookId,
                        'publisher' => $bookData['publisher'],
                        'description' => $bookData['description'],
                    ]
                ]
            );
            
            if (!$book->authors()->where('author_id', $author->id)->exists()) {
                $book->authors()->attach($author->id);
            }

            // Volumes
            $createdVolumes = [];
            for ($i = 1; $i <= max(1, $bookData['total_volumes']); $i++) {
                $vol = Volume::updateOrCreate(
                    ['book_id' => $book->id, 'volume_number' => $i],
                    ['name' => "Volume {$i}"]
                );
                $createdVolumes[$i] = $vol->id;
            }

            // Chapters
            Chapter::where('book_id', $book->id)->delete();
            $this->info("🗂 Importing " . count($bookData['chapters']) . " chapters...");
            
            foreach ($bookData['chapters'] as $chap) {
                Chapter::create([
                    'book_id' => $book->id,
                    'title' => mb_substr($chap['title'], 0, 250),
                    'page_number' => $chap['start_page'],
                    'level' => $chap['level'] ?? 1,
                ]);
            }
            
            DB::commit();
            $this->info("✅ Structure saved successfully.");

            if ($skipPages) {
                return 0;
            }

            // 3. Import Pages
            $this->info("📄 Starting page import...");
            $batchSize = 20;
            $start = 1;
            // Use estimate but be flexible
            $maxPages = $bookData['total_pages_estimate']; 
            
            // Simple progress bar
            $bar = $this->output->createProgressBar($maxPages);
            $bar->start();

            while (true) {
                $end = $start + $batchSize - 1;
                
                $pages = $this->scraper->fetchPagesParallel($bookId, $start, $end, $bookData['chapters']);
                
                if (empty($pages)) {
                    // Try one more small batch to confirm end if inside expected range
                    if ($start < $maxPages) {
                         // warning? no, just break for now.
                    }
                    break;
                }

                DB::beginTransaction();
                foreach ($pages as $pageNum => $text) {
                    Page::updateOrCreate(
                        ['book_id' => $book->id, 'page_number' => $pageNum],
                        [
                            'content' => $text,
                            'volume_id' => $createdVolumes[1] ?? null // Naive volume mapping needs improvement later
                        ]
                    );
                }
                DB::commit();

                $bar->advance(count($pages));
                
                if (count($pages) < $batchSize) {
                    // Start next batch from where we left off? 
                    // No, if we got partial batch, we likely reached end.
                    break; 
                }

                $start += $batchSize;
                
                if ($delay > 0) {
                    usleep($delay * 1000);
                }
                
                // Safety break
                if ($start > 50000) break;
            }

            $bar->finish();
            $this->newLine();
            $this->info("🎉 Import completed!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error: " . $e->getMessage());
            Log::error($e);
            return 1;
        }

        return 0;
    }
}
