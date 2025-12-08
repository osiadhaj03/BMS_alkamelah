<?php

namespace App\Services\BookReader;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Mews\Purifier\Facades\Purifier;

/**
 * Page Loader Service
 * 
 * خدمة تحميل الصفحات - تتعامل مع تحميل ومعالجة محتوى الصفحات
 */
class PageLoaderService
{
    /**
     * مدة التخزين المؤقت (ساعة واحدة)
     */
    private const CACHE_TTL = 60 * 60;

    /**
     * عدد الصفحات للتحميل المسبق
     */
    private const PREFETCH_RANGE = 2;

    /**
     * Load page with smart caching
     * 
     * @param int $bookId
     * @param int $pageNumber
     * @param bool $showDiacritics
     * @return array
     */
    public function loadPage(int $bookId, int $pageNumber, bool $showDiacritics = true): array
    {
        $cacheKey = "book_reader:{$bookId}:page:{$pageNumber}";

        $page = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bookId, $pageNumber) {
            return Page::where('book_id', $bookId)
                ->where('page_number', $pageNumber)
                ->with(['chapter:id,title', 'volume:id,number,title'])
                ->first();
        });

        if (!$page) {
            return ['page' => null, 'content' => '', 'found' => false];
        }

        $content = $this->processContent($page->content ?? '', $showDiacritics);

        // تحميل مسبق للصفحات المجاورة (في الخلفية)
        $this->schedulePrefetch($bookId, $pageNumber);

        return [
            'page' => $page,
            'content' => $content,
            'found' => true,
            'chapter_title' => $page->chapter?->title,
            'volume_title' => $page->volume 
                ? ($page->volume->title ?: "الجزء {$page->volume->number}")
                : null,
        ];
    }

    /**
     * Process and sanitize content
     * 
     * @param string $content
     * @param bool $showDiacritics
     * @return string
     */
    public function processContent(string $content, bool $showDiacritics = true): string
    {
        if (empty($content)) {
            return '';
        }

        // Sanitize HTML - السماح فقط بالعناصر الآمنة
        if (class_exists('Mews\Purifier\Facades\Purifier')) {
            $content = Purifier::clean($content, [
                'HTML.Allowed' => 'p,br,strong,em,b,i,u,h1,h2,h3,h4,h5,h6,ul,ol,li,blockquote,span,div,sup,sub,table,tr,td,th,thead,tbody',
                'HTML.AllowedAttributes' => 'class,id,style',
                'CSS.AllowedProperties' => 'color,font-weight,text-align,margin,padding',
                'AutoFormat.RemoveEmpty' => false,
            ]);
        }

        // إزالة التشكيل إذا لم يكن مطلوباً
        if (!$showDiacritics) {
            $content = $this->removeDiacritics($content);
        }

        // تنسيق الفقرات بشكل ذكي
        $content = $this->formatParagraphs($content);

        return $content;
    }

    /**
     * Remove Arabic diacritics (التشكيل)
     * 
     * @param string $text
     * @return string
     */
    public function removeDiacritics(string $text): string
    {
        // Arabic diacritics Unicode range
        $diacritics = [
            '/[\x{064B}]/u', // Fathatan
            '/[\x{064C}]/u', // Dammatan
            '/[\x{064D}]/u', // Kasratan
            '/[\x{064E}]/u', // Fatha
            '/[\x{064F}]/u', // Damma
            '/[\x{0650}]/u', // Kasra
            '/[\x{0651}]/u', // Shadda
            '/[\x{0652}]/u', // Sukun
            '/[\x{0653}]/u', // Maddah
            '/[\x{0654}]/u', // Hamza Above
            '/[\x{0655}]/u', // Hamza Below
            '/[\x{0656}]/u', // Subscript Alef
            '/[\x{0657}]/u', // Inverted Damma
            '/[\x{0658}]/u', // Mark Noon Ghunna
            '/[\x{0659}]/u', // Zwarakay
            '/[\x{065A}]/u', // Vowel Sign Small V Above
            '/[\x{065B}]/u', // Vowel Sign Inverted Small V Above
            '/[\x{065C}]/u', // Vowel Sign Dot Below
            '/[\x{065D}]/u', // Reversed Damma
            '/[\x{065E}]/u', // Fatha with Two Dots
            '/[\x{065F}]/u', // Wavy Hamza Below
            '/[\x{0670}]/u', // Superscript Alef
        ];

        return preg_replace($diacritics, '', $text);
    }

    /**
     * Smart paragraph formatting
     * 
     * @param string $content
     * @return string
     */
    private function formatParagraphs(string $content): string
    {
        // Normalize line breaks
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // إزالة الأسطر الفارغة بعد علامات الترقيم غير النهائية
        $patterns = [
            '/([،,])\s*\n\s*/' => '$1 ',     // بعد الفاصلة
            '/([؛;])\s*\n\s*/' => '$1 ',     // بعد الفاصلة المنقوطة
            '/([:])\s*\n\s*/' => '$1 ',      // بعد النقطتين
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        // الحفاظ على الفقرات (سطرين فارغين)
        $content = preg_replace('/\n\n+/', "\n\n", $content);

        // تحويل الأسطر الجديدة إلى <br>
        $content = nl2br($content);

        return $content;
    }

    /**
     * Schedule prefetch for adjacent pages
     * 
     * @param int $bookId
     * @param int $currentPage
     * @return void
     */
    private function schedulePrefetch(int $bookId, int $currentPage): void
    {
        // يمكن تنفيذ هذا لاحقاً باستخدام Jobs
        // حالياً نتركه فارغاً للبساطة
    }

    /**
     * Prefetch adjacent pages (for better performance)
     * 
     * @param int $bookId
     * @param int $currentPage
     * @param int $range
     * @return void
     */
    public function prefetchPages(int $bookId, int $currentPage, int $range = 2): void
    {
        $pages = range(
            max(1, $currentPage - $range),
            $currentPage + $range
        );

        foreach ($pages as $pageNumber) {
            if ($pageNumber === $currentPage) {
                continue;
            }

            $cacheKey = "book_reader:{$bookId}:page:{$pageNumber}";

            if (!Cache::has($cacheKey)) {
                Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bookId, $pageNumber) {
                    return Page::where('book_id', $bookId)
                        ->where('page_number', $pageNumber)
                        ->with(['chapter:id,title', 'volume:id,number,title'])
                        ->first();
                });
            }
        }
    }

    /**
     * Clear page cache
     * 
     * @param int $bookId
     * @param int|null $pageNumber
     * @return void
     */
    public function clearCache(int $bookId, ?int $pageNumber = null): void
    {
        if ($pageNumber) {
            Cache::forget("book_reader:{$bookId}:page:{$pageNumber}");
        } else {
            // مسح كل صفحات الكتاب من الكاش
            // ملاحظة: هذا يتطلب Redis أو driver يدعم pattern matching
            // للآن نتركه بسيطاً
        }
    }
}
