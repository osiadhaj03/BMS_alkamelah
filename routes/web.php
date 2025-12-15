<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookReaderController;

Route::get('/', [HomeController::class, 'index'])->name('home');

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
 