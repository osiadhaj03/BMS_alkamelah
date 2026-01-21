<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookSection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * أمر استيراد الكتب من ملفات الأقسام
 * 
 * الاستخدام:
 * php artisan turath:import-categories
 * php artisan turath:import-categories --file="categories/1_العقيدة.txt"
 * php artisan turath:import-categories --limit=10
 */
class ImportTurathCategories extends Command
{
    protected $signature = 'turath:import-categories
                            {--file= : ملف محدد للاستيراد (اختياري)}
                            {--limit=0 : عدد الكتب للاستيراد (0 = الكل)}
                            {--delay=1000 : التأخير بين الكتب بالميلي ثانية}';

    protected $description = 'استيراد الكتب من ملفات الأقسام في مجلد categories';

    protected array $stats = [
        'success' => 0,
        'failed' => 0,
        'skipped' => 0,
    ];

    public function handle(): int
    {
        set_time_limit(0);

        $specificFile = $this->option('file');
        $limit = (int) $this->option('limit');
        $delay = (int) $this->option('delay');

        $categoriesPath = base_path('categories');

        if (!is_dir($categoriesPath)) {
            $this->error("❌ مجلد categories غير موجود: {$categoriesPath}");
            return Command::FAILURE;
        }

        // Get files to process
        if ($specificFile) {
            $files = [base_path($specificFile)];
        } else {
            $files = glob($categoriesPath . '/*.txt');
        }

        if (empty($files)) {
            $this->error('❌ لم يتم العثور على ملفات في مجلد categories');
            return Command::FAILURE;
        }

        $this->printHeader(count($files));

        foreach ($files as $file) {
            if (!file_exists($file)) {
                $this->warn("⚠️ الملف غير موجود: {$file}");
                continue;
            }

            $this->processFile($file, $limit, $delay);

            if ($limit > 0 && ($this->stats['success'] + $this->stats['failed']) >= $limit) {
                $this->info("\n🛑 تم الوصول للحد الأقصى: {$limit} كتاب");
                break;
            }
        }

        $this->printSummary();

        return Command::SUCCESS;
    }

    protected function processFile(string $filePath, int $limit, int $delay): void
    {
        $filename = basename($filePath);
        $sectionName = $this->extractSectionName($filename);

        $this->newLine();
        $this->info("═══════════════════════════════════════════════════════");
        $this->info("📂 معالجة الملف: {$filename}");
        $this->info("📁 القسم: {$sectionName}");
        $this->info("═══════════════════════════════════════════════════════");

        // Find section in database
        $section = BookSection::where('name', 'LIKE', "%{$sectionName}%")->first();

        if (!$section) {
            $this->warn("⚠️ القسم غير موجود في قاعدة البيانات: {$sectionName}");
            // Continue anyway, just won't assign section
        } else {
            $this->info("✅ تم العثور على القسم: {$section->name} (ID: {$section->id})");
        }

        // Read file content
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $updatedLines = [];
        $processed = 0;

        foreach ($lines as $lineIndex => $line) {
            $originalLine = $line;

            // Skip already processed lines
            if (str_contains($line, '✅') || str_contains($line, '❌') || str_contains($line, '⏭️')) {
                $updatedLines[] = $line;
                continue;
            }

            // Extract book ID
            $bookId = trim($line);
            if (!is_numeric($bookId)) {
                $updatedLines[] = $line;
                continue;
            }

            $bookId = (int) $bookId;
            $processed++;

            $this->line("\n📖 [{$processed}] استيراد كتاب ID: {$bookId}");

            // Check if book already exists
            $existingBook = Book::where('shamela_id', $bookId)->first();
            if ($existingBook) {
                $this->warn("   🗑️ حذف الكتاب القديم: {$existingBook->title}");
                $existingBook->pages()->delete();
                $existingBook->chapters()->delete();
                $existingBook->volumes()->delete();
                $existingBook->authors()->detach();
                $existingBook->delete();
            }

            // Import book using existing command (with full output)
            try {
                $this->newLine();
                $exitCode = $this->call('turath:import', [
                    'book_id' => $bookId,
                    '--force' => true,
                    '--delay' => $delay,
                ]);

                if ($exitCode === 0) {
                    // Update section if found
                    if ($section) {
                        $newBook = Book::where('shamela_id', $bookId)->first();
                        if ($newBook) {
                            $newBook->book_section_id = $section->id;
                            $newBook->save();
                        }
                    }

                    $this->stats['success']++;
                    $updatedLines[] = "{$bookId} ✅";
                    $this->info("   ✅ تم الاستيراد بنجاح");
                } else {
                    $this->stats['failed']++;
                    $updatedLines[] = "{$bookId} ❌ [فشل الاستيراد]";
                    $this->error("   ❌ فشل الاستيراد");
                }

            } catch (\Exception $e) {
                $this->stats['failed']++;
                $errorMsg = mb_substr($e->getMessage(), 0, 50);
                $updatedLines[] = "{$bookId} ❌ [{$errorMsg}]";
                $this->error("   ❌ خطأ: {$e->getMessage()}");
            }

            // Save progress after each book
            file_put_contents($filePath, implode("\n", $updatedLines) . "\n");

            // Check limit
            if ($limit > 0 && ($this->stats['success'] + $this->stats['failed']) >= $limit) {
                // Add remaining lines unchanged
                for ($i = $lineIndex + 1; $i < count($lines); $i++) {
                    $updatedLines[] = $lines[$i];
                }
                break;
            }

            // Delay between books
            usleep($delay * 1000);
        }

        // Final save
        file_put_contents($filePath, implode("\n", $updatedLines) . "\n");
    }

    protected function extractSectionName(string $filename): string
    {
        // Remove extension
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Remove leading number and underscore (e.g., "1_العقيدة" -> "العقيدة")
        if (preg_match('/^\d+_(.+)$/', $name, $matches)) {
            return $matches[1];
        }

        return $name;
    }

    protected function printHeader(int $fileCount): void
    {
        $this->newLine();
        $this->line('╔═══════════════════════════════════════════════════════╗');
        $this->line('║  🚀 استيراد الكتب من ملفات الأقسام                   ║');
        $this->line('╠═══════════════════════════════════════════════════════╣');
        $this->line("║  📂 عدد الملفات: {$fileCount}                                    ║");
        $this->line('╚═══════════════════════════════════════════════════════╝');
    }

    protected function printSummary(): void
    {
        $this->newLine(2);
        $this->line('╔═══════════════════════════════════════════════════════╗');
        $this->line('║  📊 ملخص الاستيراد                                    ║');
        $this->line('╠═══════════════════════════════════════════════════════╣');
        $this->line("║  ✅ نجح: {$this->stats['success']}                                           ║");
        $this->line("║  ❌ فشل: {$this->stats['failed']}                                           ║");
        $this->line('╚═══════════════════════════════════════════════════════╝');
        $this->newLine();
    }
}
