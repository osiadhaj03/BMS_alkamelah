<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة سحب الكتب من Turath.io
 * 
 * تستخدم API الخاص بموقع Turath.io لجلب معلومات الكتب ومحتوى الصفحات
 */
class TurathScraperService
{
    /**
     * رابط API الأساسي
     */
    protected string $baseUrl = 'https://api.turath.io';

    /**
     * التأخير بين الطلبات (بالميلي ثانية)
     */
    protected int $delayMs = 500;

    /**
     * عدد محاولات إعادة الطلب
     */
    protected int $maxRetries = 3;

    /**
     * مهلة الطلب (بالثواني)
     */
    protected int $timeout = 30;

    /**
     * Callback لعرض التقدم
     */
    protected $progressCallback = null;

    /**
     * تعيين callback للتقدم
     */
    public function setProgressCallback(callable $callback): self
    {
        $this->progressCallback = $callback;
        return $this;
    }

    /**
     * تعيين التأخير بين الطلبات
     */
    public function setDelay(int $ms): self
    {
        $this->delayMs = $ms;
        return $this;
    }

    /**
     * جلب معلومات الكتاب من API
     * 
     * @param int $bookId معرف الكتاب في Turath.io
     * @return array|null بيانات الكتاب أو null في حالة الفشل
     */
    public function getBookInfo(int $bookId): ?array
    {
        $url = "{$this->baseUrl}/book";

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept' => 'application/json',
                    ])
                    ->get($url, [
                        'id' => $bookId,
                        'include' => 'indexes',
                        'ver' => 3,
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::warning("Turath API: فشل جلب معلومات الكتاب {$bookId}", [
                    'status' => $response->status(),
                    'attempt' => $attempt,
                ]);

            } catch (\Exception $e) {
                Log::error("Turath API: خطأ في الاتصال", [
                    'book_id' => $bookId,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);
            }

            if ($attempt < $this->maxRetries) {
                $delay = 1000 * pow(2, $attempt - 1); // Exponential backoff
                usleep($delay * 1000);
            }
        }

        return null;
    }

    /**
     * جلب محتوى صفحة واحدة
     * 
     * @param int $bookId معرف الكتاب
     * @param int $pageNumber رقم الصفحة
     * @return array|null بيانات الصفحة أو null
     */
    public function getPageContent(int $bookId, int $pageNumber): ?array
    {
        $url = "{$this->baseUrl}/page";

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'application/json',
                ])
                ->get($url, [
                    'book_id' => $bookId,
                    'pg' => $pageNumber,
                    'ver' => 3,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // فك بيانات الـ meta إذا كانت موجودة كنص JSON
                if (isset($data['meta']) && is_string($data['meta'])) {
                    $decodedMeta = json_decode($data['meta'], true);
                    if ($decodedMeta) {
                        $data['parsed_meta'] = $decodedMeta;
                    }
                }

                return $data;
            }

        } catch (\Exception $e) {
            Log::debug("Turath API: فشل جلب الصفحة {$pageNumber}", [
                'book_id' => $bookId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * جلب جميع صفحات الكتاب
     * 
     * @param int $bookId معرف الكتاب
     * @param int $startPage الصفحة الأولى
     * @param int $endPage الصفحة الأخيرة
     * @return \Generator مولد للصفحات
     */
    public function getAllPages(int $bookId, int $startPage = 1, ?int $endPage = null, ?array $pageMap = null): \Generator
    {
        $pageNumber = $startPage;
        $emptyPagesCount = 0;
        $maxEmptyPages = 3;

        // إذا تم توفير page_map، نستخدم طولها كحد أقصى للطلبات
        $totalToFetch = $endPage;
        if ($pageMap && count($pageMap) > 0) {
            if ($endPage !== null) {
                $totalToFetch = min($endPage, count($pageMap));
            } else {
                $totalToFetch = count($pageMap);
            }
        }

        while (true) {
            // التحقق من الوصول لنهاية الكتاب
            if ($totalToFetch !== null && $pageNumber > $totalToFetch) {
                break;
            }

            // جلب محتوى الصفحة
            $pageData = $this->getPageContent($bookId, $pageNumber);

            // فشل الطلب = نهاية الكتاب
            if ($pageData === null) {
                break;
            }

            // تنظيف النص
            $text = $this->cleanText($pageData['text'] ?? '');

            // استخراج البيانات من الـ meta المفسر
            $parsedMeta = $pageData['parsed_meta'] ?? [];
            $originalPage = $parsedMeta['page'] ?? $pageNumber;
            $volumeNumber = $parsedMeta['vol'] ?? 1;

            // صفحة فارغة
            if (empty($text)) {
                $emptyPagesCount++;
                if ($emptyPagesCount >= $maxEmptyPages && !$pageMap) {
                    break;
                }
                $pageNumber++;
                usleep($this->delayMs * 1000);
                continue;
            }

            // إعادة تعيين عداد الصفحات الفارغة
            $emptyPagesCount = 0;

            // إرسال التقدم
            if ($this->progressCallback) {
                call_user_func($this->progressCallback, $pageNumber, $totalToFetch);
            }

            // إرجاع الصفحة مع بياناتها الوصفية
            yield [
                'page_number' => $pageNumber, // التسلسلي
                'original_page_number' => (int) $originalPage, // المطبوع
                'volume_number' => (int) $volumeNumber, // المجلد
                'content' => $text,
                'raw_content' => $pageData['text'] ?? '',
                'meta' => $parsedMeta,
            ];

            $pageNumber++;

            // التأخير بين الطلبات
            usleep($this->delayMs * 1000);
        }
    }

    /**
     * جلب صفحات متعددة بشكل متوازي
     *
     * @param int $bookId معرف الكتاب
     * @param array $pages قائمة أرقام الصفحات (sequential)
     * @param int $concurrency عدد الطلبات المتزامنة
     * @return array نتائج الصفحات
     */
    public function fetchPagesParallel(int $bookId, array $pages, int $concurrency = 10): array
    {
        $results = [];
        $chunks = array_chunk($pages, $concurrency);

        foreach ($chunks as $chunk) {
            $responses = Http::pool(
                fn($pool) =>
                collect($chunk)->map(
                    fn($page) =>
                    $pool->as("page_{$page}")
                        ->timeout($this->timeout)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                            'Accept' => 'application/json',
                        ])
                        ->get("{$this->baseUrl}/page", [
                            'book_id' => $bookId,
                            'pg' => $page,
                            'ver' => 3,
                        ])
                )
            );

            foreach ($responses as $key => $response) {
                $pageNum = (int) str_replace('page_', '', $key);

                if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                    $pageData = $response->json();

                    // Parse Meta
                    if (isset($pageData['meta']) && is_string($pageData['meta'])) {
                        $pageData['parsed_meta'] = json_decode($pageData['meta'], true);
                    }

                    $text = $this->cleanText($pageData['text'] ?? '');

                    // Only yield valid pages
                    $results[$pageNum] = [
                        'page_number' => $pageNum,
                        'original_page_number' => $pageData['parsed_meta']['page'] ?? $pageNum,
                        'volume_number' => $pageData['parsed_meta']['vol'] ?? 1,
                        'content' => $text,
                    ];
                } else {
                    $error = ($response instanceof \Exception) ? $response->getMessage() : "HTTP " . $response->status();
                    Log::warning("Turath Parallel: Failed page {$pageNum} - Reason: {$error}");
                }
            }

            // Short delay to respect server limits
            usleep($this->delayMs * 1000); // 250ms or 500ms
        }

        return $results;
    }

    /**
     * الحصول على عدد الصفحات الإجمالي من volume_bounds
     * 
     * @param array $volumeBounds بيانات حدود المجلدات
     * @return int عدد الصفحات
     */
    public function getTotalPages(array $volumeBounds): int
    {
        $maxPage = 0;
        foreach ($volumeBounds as $bounds) {
            if (is_array($bounds) && isset($bounds[1])) {
                $maxPage = max($maxPage, $bounds[1]);
            }
        }
        return $maxPage;
    }

    /**
     * الحصول على معلومات المجلدات
     * 
     * @param array $volumeBounds بيانات حدود المجلدات
     * @return array معلومات المجلدات
     */
    public function parseVolumes(array $volumeBounds): array
    {
        $volumes = [];
        foreach ($volumeBounds as $volumeNumber => $bounds) {
            if (is_array($bounds) && count($bounds) >= 2) {
                $volumes[] = [
                    'number' => (int) $volumeNumber,
                    'page_start' => $bounds[0],
                    'page_end' => $bounds[1],
                    'total_pages' => $bounds[1] - $bounds[0] + 1,
                ];
            }
        }
        return $volumes;
    }

    /**
     * تحويل الفهرس (headings) إلى فصول
     * 
     * @param array $headings فهرس الكتاب
     * @return array الفصول
     */
    public function parseChapters(array $headings): array
    {
        $chapters = [];
        $order = 0;

        foreach ($headings as $heading) {
            $order++;
            $chapters[] = [
                'title' => $heading['title'] ?? '',
                'level' => $heading['level'] ?? 1,
                'page_start' => $heading['page'] ?? null,
                'order' => $order,
            ];
        }

        return $chapters;
    }

    /**
     * تنظيف النص من HTML والرموز الخاصة
     * 
     * @param string $text النص الأصلي
     * @return string النص المنظف
     */
    public function cleanText(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return trim(
            preg_replace(
                [
                    '/<[^>]*>/',           // إزالة HTML tags
                    '/&nbsp;/',            // تحويل &nbsp;
                    '/&amp;/',             // تحويل &amp;
                    '/&lt;/',              // تحويل &lt;
                    '/&gt;/',              // تحويل &gt;
                    '/&quot;/',            // تحويل &quot;
                    '/&#39;/',             // تحويل &#39;
                    '/\r\n/',              // توحيد نهاية الأسطر
                    '/\n{3,}/',            // تقليل الأسطر الفارغة
                ],
                [
                    '',
                    ' ',
                    '&',
                    '<',
                    '>',
                    '"',
                    "'",
                    "\n",
                    "\n\n",
                ],
                $text
            )
        );
    }
}
