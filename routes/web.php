<?php

use App\Http\Controllers\HomeController;
use App\Livewire\BookReader\BookReaderPage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Show All Route - for listing all books or authors with optional section filtering
Route::get('/show-all/{type?}', [HomeController::class, 'showAll'])->name('show-all');

// Categories Route - for displaying all book categories/sections
Route::get('/categories', [HomeController::class, 'categories'])->name('categories');


// ═══════════════════════════════════════════════════════════
// Book Reader Routes
// ═══════════════════════════════════════════════════════════

///Route::get('/book/{bookId}/{pageNumber?}', BookReaderPage::class)
///    ->where('bookId', '[0-9]+')
///    ->where('pageNumber', '[0-9]+')
///    ->name('book.read');
///
///// Alternative route with slug
///Route::get('/read/{bookSlug}/{pageNumber?}', BookReaderPage::class)
///    ->where('pageNumber', '[0-9]+')
///    ->name('book.read.slug');
///
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

// مسار للتحقق من محتوى AuthorForm - احذفه بعد الاستخدام
Route::get('/check-author-form', function () {
    $file = app_path('Filament/Resources/Authors/Schemas/AuthorForm.php');
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $hasRichEditor = str_contains($content, 'RichEditor::make');
        $hasSection = str_contains($content, "Section::make('المعلومات الأساسية')");
        $hasSourceAi = str_contains($content, 'source-ai');
        
        return "
            <h2>AuthorForm.php Check:</h2>
            <ul>
                <li>File exists: ✅</li>
                <li>Has RichEditor: " . ($hasRichEditor ? '✅' : '❌') . "</li>
                <li>Has Arabic Section: " . ($hasSection ? '✅' : '❌') . "</li>
                <li>Has source-ai button: " . ($hasSourceAi ? '✅' : '❌') . "</li>
            </ul>
            <p>File modified: " . date('Y-m-d H:i:s', filemtime($file)) . "</p>
        ";
    }
    return 'File not found! ❌';
});
