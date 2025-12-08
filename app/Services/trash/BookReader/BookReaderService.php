<?php

namespace App\Services\BookReader;

use App\Models\Book;
use App\Models\Page;
use App\Models\Chapter;
use App\Models\Volume;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * Book Reader Service
 * 
 * خدمة قراءة الكتب - تتعامل مع منطق الأعمال الرئيسي
 */
class BookReaderService
{
    /**
     * مدة التخزين المؤقت (6 ساعات)
     */
    private const CACHE_TTL = 60 * 60 * 6;

    /**
     * Get book with essential relationships
     * 
     * @param int $bookId
     * @return Book|null
     */
    public function getBook(int $bookId): ?Book
    {
        return Cache::remember(
            "book_reader:{$bookId}:book",
            self::CACHE_TTL,
            fn() => Book::with([
                'authors' => fn($q) => $q->orderByPivot('display_order'),
                'bookSection',
            ])
            ->where('visibility', 'public')
            ->find($bookId)
        );
    }

    /**
     * Get total pages count (cached)
     * 
     * @param int $bookId
     * @return int
     */
    public function getTotalPages(int $bookId): int
    {
        return Cache::remember(
            "book_reader:{$bookId}:total_pages",
            self::CACHE_TTL,
            fn() => Page::where('book_id', $bookId)->count()
        );
    }

    /**
     * Get volumes for a book
     * 
     * @param int $bookId
     * @return Collection
     */
    public function getVolumes(int $bookId): Collection
    {
        return Cache::remember(
            "book_reader:{$bookId}:volumes",
            self::CACHE_TTL,
            fn() => Volume::where('book_id', $bookId)
                ->orderBy('number')
                ->get(['id', 'number', 'title', 'page_start', 'page_end'])
        );
    }

    /**
     * Get table of contents (cached)
     * 
     * @param int $bookId
     * @return array
     */
    public function getTableOfContents(int $bookId): array
    {
        return Cache::remember(
            "book_reader:{$bookId}:toc",
            self::CACHE_TTL,
            fn() => $this->buildTableOfContents($bookId)
        );
    }

    /**
     * Build table of contents structure
     * 
     * @param int $bookId
     * @return array
     */
    private function buildTableOfContents(int $bookId): array
    {
        $volumes = Volume::where('book_id', $bookId)
            ->with(['chapters' => fn($q) => $q->whereNull('parent_id')->orderBy('order')->with('children')])
            ->orderBy('number')
            ->get();

        // إذا لا توجد مجلدات، نحمل الفصول مباشرة
        if ($volumes->isEmpty()) {
            $chapters = Chapter::where('book_id', $bookId)
                ->whereNull('parent_id')
                ->with('allChildren')
                ->orderBy('order')
                ->get();

            return [
                'type' => 'chapters_only',
                'data' => $this->formatChaptersForToc($chapters),
            ];
        }

        return [
            'type' => 'volumes',
            'data' => $volumes->map(fn($vol) => [
                'id' => $vol->id,
                'number' => $vol->number,
                'title' => $vol->title ?: "الجزء {$vol->number}",
                'page_start' => $vol->page_start,
                'chapters' => $this->formatChaptersForToc($vol->chapters),
            ])->toArray(),
        ];
    }

    /**
     * Format chapters recursively for TOC
     * 
     * @param Collection $chapters
     * @param int $level
     * @return array
     */
    private function formatChaptersForToc(Collection $chapters, int $level = 0): array
    {
        return $chapters->map(fn($ch) => [
            'id' => $ch->id,
            'title' => $ch->title,
            'page_start' => $ch->page_start,
            'level' => $level,
            'has_children' => $ch->children->isNotEmpty(),
            'children' => $ch->children->isNotEmpty()
                ? $this->formatChaptersForToc($ch->children, $level + 1)
                : [],
        ])->toArray();
    }

    /**
     * Search in book content
     * 
     * @param int $bookId
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchInBook(int $bookId, string $query, int $limit = 50): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        return Page::where('book_id', $bookId)
            ->where('content', 'LIKE', "%{$query}%")
            ->select(['id', 'page_number', 'content', 'chapter_id', 'volume_id'])
            ->with(['chapter:id,title', 'volume:id,number,title'])
            ->limit($limit)
            ->get()
            ->map(fn($page) => [
                'page_number' => $page->page_number,
                'excerpt' => $this->extractExcerpt($page->content, $query),
                'chapter' => $page->chapter?->title,
                'volume' => $page->volume 
                    ? ($page->volume->title ?: "الجزء {$page->volume->number}")
                    : null,
            ])
            ->toArray();
    }

    /**
     * Extract excerpt around search term
     * 
     * @param string $content
     * @param string $query
     * @param int $length
     * @return string
     */
    private function extractExcerpt(string $content, string $query, int $length = 150): string
    {
        $content = strip_tags($content);
        $pos = mb_stripos($content, $query);

        if ($pos === false) {
            return mb_substr($content, 0, $length) . '...';
        }

        $start = max(0, $pos - 60);
        $excerpt = mb_substr($content, $start, $length);

        // Highlight the query
        $excerpt = preg_replace(
            '/(' . preg_quote($query, '/') . ')/iu',
            '<mark class="search-highlight">$1</mark>',
            $excerpt
        );

        return ($start > 0 ? '...' : '') . $excerpt . '...';
    }

    /**
     * Get first page of volume
     * 
     * @param int $volumeId
     * @return int|null
     */
    public function getFirstPageOfVolume(int $volumeId): ?int
    {
        $volume = Volume::find($volumeId);
        
        if ($volume && $volume->page_start) {
            return $volume->page_start;
        }

        return Page::where('volume_id', $volumeId)
            ->orderBy('page_number')
            ->value('page_number');
    }

    /**
     * Get chapter start page
     * 
     * @param int $chapterId
     * @return int|null
     */
    public function getChapterStartPage(int $chapterId): ?int
    {
        $chapter = Chapter::find($chapterId);
        
        if ($chapter && $chapter->page_start) {
            return $chapter->page_start;
        }

        return Page::where('chapter_id', $chapterId)
            ->orderBy('page_number')
            ->value('page_number');
    }

    /**
     * Get navigation info for current page
     * 
     * @param int $bookId
     * @param int $pageNumber
     * @return array
     */
    public function getNavigationInfo(int $bookId, int $pageNumber): array
    {
        $totalPages = $this->getTotalPages($bookId);

        return [
            'current_page' => $pageNumber,
            'total_pages' => $totalPages,
            'has_previous' => $pageNumber > 1,
            'has_next' => $pageNumber < $totalPages,
            'progress_percentage' => $totalPages > 0 
                ? round(($pageNumber / $totalPages) * 100, 1) 
                : 0,
        ];
    }

    /**
     * Clear cache for a book
     * 
     * @param int $bookId
     * @return void
     */
    public function clearCache(int $bookId): void
    {
        Cache::forget("book_reader:{$bookId}:book");
        Cache::forget("book_reader:{$bookId}:total_pages");
        Cache::forget("book_reader:{$bookId}:volumes");
        Cache::forget("book_reader:{$bookId}:toc");
    }
}
