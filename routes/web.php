<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// مسار مؤقت لمسح الـ Cache - احذفه بعد الاستخدام
Route::get('/clear-cache-secret-2024', function () {
    Artisan::call('optimize:clear');
    Artisan::call('filament:optimize-clear');
    
    // حذف ملفات views المؤقتة يدوياً
    $viewsPath = storage_path('framework/views');
    $files = glob($viewsPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    
    return 'All caches cleared successfully! ✅<br><br>
            <strong>Important:</strong> Delete this route from routes/web.php after use.';
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
