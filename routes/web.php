<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookReaderController;
use App\Http\Controllers\NewsletterSubscriberController;
use App\Http\Controllers\FeedbackComplaintController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleCommentController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/import-turath', \App\Livewire\ImportTurathPage::class)->name('import.turath');
// Route::get('/import-categories', \App\Livewire\ImportCategoryPage::class)->name('import.categories');
Route::get('/import-ketab-online', \App\Livewire\ImportKetabOnlinePage::class)->name('import.ketab-online');
Route::get('/category', [HomeController::class, 'categories'])->name('categories.index');
Route::get('/authors', [HomeController::class, 'authors'])->name('authors.index');
Route::get('/author/{id}', [HomeController::class, 'authorShow'])->name('author.show');
Route::get('/books', [HomeController::class, 'books'])->name('books.index');
Route::view('/about-us', 'pages.about')->name('about');
Route::post('/newsletter/subscribe', [NewsletterSubscriberController::class, 'store'])->name('newsletter.subscribe');

Route::view('/feedback', 'pages.feedback')->name('feedback');
Route::post('/feedback/store', [FeedbackComplaintController::class, 'store'])->name('feedback.store');

// News Routes
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
    Route::get('/category/{category}', [NewsController::class, 'byCategory'])->name('category');
});

// Articles Routes
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('show');
    Route::get('/category/{category}', [ArticleController::class, 'byCategory'])->name('category');
    Route::post('/{article}/like', [ArticleController::class, 'like'])->name('like');
    Route::post('/{article}/comment', [ArticleCommentController::class, 'store'])->name('comment.store');
});

// Deployment Script Route (Protected)
Route::get('/deploy-fix-composer', function () {
    // Security: Only run in production and with secret token
    $token = request()->query('token');
    $expectedToken = config('app.deploy_token', 'your-secret-token-here');

    if ($token !== $expectedToken) {
        abort(403, 'Unauthorized');
    }

    $scriptPath = base_path('fix-server-composer.sh');

    if (!file_exists($scriptPath)) {
        return response()->json(['error' => 'Script not found'], 404);
    }

    // Execute the script
    $output = [];
    $returnCode = 0;
    exec("bash {$scriptPath} 2>&1", $output, $returnCode);

    return response()->json([
        'success' => $returnCode === 0,
        'return_code' => $returnCode,
        'output' => implode("\n", $output),
        'timestamp' => now()->toDateTimeString(),
    ]);
})->name('deploy.fix-composer');

// Search Page Prototype
Route::get('/search', function () {
    return view('pages.search.advanced-search');
})->name('search.index');

// Book Reader Routes
Route::get('/book/{bookId}/{pageNumber?}', [BookReaderController::class, 'show'])
    ->name('book.read')
    ->where(['bookId' => '[0-9]+', 'pageNumber' => '[0-9]+']);

// Book Search API (for in-book search)
Route::get('/book/{bookId}/search', [BookReaderController::class, 'search'])
    ->name('book.search')
    ->where(['bookId' => '[0-9]+']);

// Static Book Preview Route
Route::get('/preview/book-static', function () {
    return view('pages.book-preview');
})->name('book.preview');

// مسار مؤقت لمسح الـ Cache - احذفه بعد الاستخدام
Route::get('/clear-cache-secret-2024', function () {
    // مسح الـ cache يدوياً بدون استخدام artisan commands
    $paths = [
        storage_path('framework/views'),
        storage_path('framework/cache/data'),
        base_path('bootstrap/cache'),
    ];

    $cleared = [];

    foreach ($paths as $path) {
        if (is_dir($path)) {
            $files = glob($path . '/*');
            foreach ($files as $file) {
                if (is_file($file) && !str_contains($file, '.gitignore')) {
                    unlink($file);
                }
            }
            $cleared[] = $path;
        }
    }

    // مسح cache من جدول cache في قاعدة البيانات
    try {
        \Illuminate\Support\Facades\DB::table('cache')->truncate();
        $cleared[] = 'database cache table';
    } catch (\Exception $e) {
        // تجاهل إذا لم يكن هناك جدول cache
    }

    return '<h2>Cache Cleared Successfully! ✅</h2>
            <p>Cleared paths:</p>
            <ul><li>' . implode('</li><li>', $cleared) . '</li></ul>
            <p><strong>Important:</strong> Delete this route from routes/web.php after use.</p>
            <p><a href="/admin">Go to Admin Panel</a></p>';
});

Route::view('/static-search', 'pages.search.static-search')->name('search.static');

// ===================================================================
// FILTER API ROUTES
// ===================================================================
Route::prefix('api')->name('api.')->group(function () {

    // Books API for filter modal (with search & pagination)
    Route::get('/books', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\Book::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return $query->select('id', 'title')
            ->orderBy('title')
            ->paginate(50);
    })->name('books');

    // Authors API for filter modal (with search & pagination)
    Route::get('/authors', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\Author::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $search = $request->search;
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('laqab', 'like', '%' . $search . '%')
                    ->orWhere('kunyah', 'like', '%' . $search . '%');
            });
        }

        $results = $query->select('id', 'first_name', 'last_name', 'laqab', 'kunyah')
            ->orderBy('first_name')
            ->paginate(50);

        // Transform to add full_name
        $results->getCollection()->transform(function ($author) {
            return [
                'id' => $author->id,
                'name' => trim(implode(' ', array_filter([
                    $author->laqab,
                    $author->kunyah,
                    $author->first_name,
                    $author->last_name,
                ])))
            ];
        });

        return $results;
    })->name('authors');

    // Sections API for filter modal (load all - typically 40-100 sections)
    Route::get('/sections', function (\Illuminate\Http\Request $request) {
        $query = \App\Models\BookSection::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->select('id', 'name')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn($s) => ['id' => $s->id, 'name' => $s->name]);
    })->name('sections');

    // Ultra-Fast Search API (Elasticsearch)
    Route::get('/ultra-search', function (\Illuminate\Http\Request $request) {
        try {
            $searchService = new \App\Services\UltraFastSearchService();

            $query = $request->input('q', '');
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10);

            // Build filters array
            $filters = [
                'search_type' => $request->input('search_type', 'flexible_match'),
                'word_order' => $request->input('word_order', 'any_order'),
            ];

            // Add optional filters
            if ($request->filled('book_id')) {
                $filters['book_id'] = explode(',', $request->input('book_id'));
            }
            if ($request->filled('author_id')) {
                $filters['author_id'] = explode(',', $request->input('author_id'));
            }
            if ($request->filled('section_id')) {
                $filters['section_id'] = explode(',', $request->input('section_id'));
            }

            $results = $searchService->search($query, $filters, $page, $perPage);

            // Transform to API response format
            return response()->json([
                'success' => true,
                'data' => collect($results['results'] ?? [])->map(function ($item) {
                    return [
                        'id' => $item['id'] ?? null,
                        'book_title' => $item['book_title'] ?? '',
                        'book_description' => $item['book_description'] ?? null, // وصف الكتاب
                        'author_name' => is_array($item['author_name'] ?? null)
                            ? implode(', ', $item['author_name'])
                            : ($item['author_name'] ?? ''),
                        'page_number' => $item['page_number'] ?? null,
                        'content' => $item['content'] ?? '',
                        'highlighted_content' => $item['highlighted_content'] ?? ($item['content'] ?? ''),
                        'book_id' => $item['book_id'] ?? null,
                        'publisher' => $item['publisher'] ?? null, // الناشر
                        'total_pages' => $item['total_pages'] ?? null, // عدد الصفحات
                        'matched_terms' => $item['matched_terms'] ?? [], // الكلمات المطابقة
                    ];
                }),
                'pagination' => [
                    'current_page' => $results['current_page'] ?? $page,
                    'last_page' => $results['last_page'] ?? 1,
                    'per_page' => $results['per_page'] ?? $perPage,
                    'total' => $results['total'] ?? 0,
                ],
                'search_metadata' => $results['search_metadata'] ?? null,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ultra-search API error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Search failed: ' . $e->getMessage(),
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ]
            ], 500);
        }
    })->name('ultra-search');

    // Get Full Page Content API (for preview pane)
    Route::get('/page/{id}', function (\Illuminate\Http\Request $request, $id) {
        try {
            $page = \App\Models\Page::with(['book', 'volume', 'chapter'])->find($id);

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'error' => 'Page not found'
                ], 404);
            }

            $content = $page->html_content ?? $page->content ?? '';

            // Highlight search query if provided
            $searchQuery = $request->input('q', '');
            if (!empty($searchQuery)) {
                // Split query into words and highlight each
                $words = preg_split('/\s+/', trim($searchQuery));
                foreach ($words as $word) {
                    if (strlen($word) >= 2) {
                        // Escape special regex characters
                        $escapedWord = preg_quote($word, '/');
                        // Highlight the word
                        $content = preg_replace(
                            '/(' . $escapedWord . ')/ui',
                            '<mark class="bg-yellow-200 text-black px-0.5 rounded">$1</mark>',
                            $content
                        );
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $page->id,
                    'page_number' => $page->page_number,
                    'original_page_number' => $page->original_page_number,
                    'content' => $content,
                    'book_id' => $page->book_id,
                    'book_title' => $page->book->title ?? '',
                    'volume_title' => $page->volume->title ?? null,
                    'chapter_title' => $page->chapter->title ?? null,
                    'author_name' => $page->book->authors->first()?->laqab ??
                        $page->book->authors->first()?->first_name ?? '',
                ]
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Page API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load page'
            ], 500);
        }
    })->name('page');
});