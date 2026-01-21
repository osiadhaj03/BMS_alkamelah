<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Page;
use App\Models\Volume;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Pool;
use Symfony\Component\DomCrawler\Crawler;

class KetabOnlineScraperService
{
    protected $baseUrl = 'https://ketabonline.com';

    /**
     * Fetch book metadata and TOC from the main book page using HTML scraping.
     *
     * @param string $bookId
     * @return array
     * @throws \Exception
     */
    public function fetchBookInfo(string $bookId): array
    {
        $url = "{$this->baseUrl}/ar/books/{$bookId}";
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ])->get($url);

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch book info: HTTP " . $response->status());
        }

        $html = $response->body();
        $crawler = new Crawler($html);

        // 1. Extract Metadata
        $title = $crawler->filter('h1.strong7')->count() ? trim($crawler->filter('h1.strong7')->text()) : 'Unknown Title';
        
        $authorName = 'Unknown Author';
        $publisher = null;
        $summary = null;

        // Extract attributes from .book-attr rows
        $crawler->filter('.book-attr')->each(function (Crawler $node) use (&$authorName, &$publisher) {
            $key = $node->filter('.book-attr-key')->count() ? trim($node->filter('.book-attr-key')->text()) : '';
            $value = $node->filter('.book-attr-value')->count() ? trim($node->filter('.book-attr-value')->text()) : '';

            if (str_contains($key, 'المؤلف')) {
                $authorName = $value;
            } elseif (str_contains($key, 'الناشر')) {
                $publisher = $value;
            }
        });

        // Summary might be in a hidden div
        if ($crawler->filter('#BookDetailsText')->count()) {
            $summary = trim($crawler->filter('#BookDetailsText')->text());
        }

        // 2. Extract TOC (Chapters)
        // Structure: <div class="BookContents"><a class="OneLine" href="/ar/books/3501/read?page=X&part=Y">Title</a></div>
        $chapters = $crawler->filter('.BookContents a.OneLine')->each(function (Crawler $node) {
            $text = trim($node->text());
            $href = $node->attr('href');
            
            // Extract page and part from query string
            // Link example: /ar/books/3501/read?page=1&part=1
            $startPage = 1;
            $volume = 1;
            
            $query = parse_url($href, PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $params);
                $startPage = $params['page'] ?? 1;
                $volume = $params['part'] ?? 1;
            }

            return [
                'title' => $text,
                'start_page' => (int)$startPage,
                'volume' => (int)$volume,
                'level' => 1 // KetabOnline TOC seems flat mostly, we default to 1 for now
            ];
        });

        // Determine total pages and volumes
        $totalPages = 0;
        $totalVolumes = 0;

        if (!empty($chapters)) {
            $lastChapter = end($chapters);
            // We assume the last chapter starts near the end. 
            // Better way: we'll find the max page from all links
            $maxPage = 0;
            $maxVol = 0;
            foreach ($chapters as $chap) {
                if ($chap['start_page'] > $maxPage) $maxPage = $chap['start_page'];
                if ($chap['volume'] > $maxVol) $maxVol = $chap['volume'];
            }
            // Add some buffer or create a logic to fetch the specific last page number later
            // For now, let's use the max start page found as a baseline
            $totalPages = $maxPage + 50; // Approximation, will be corrected during import
            $totalVolumes = $maxVol;
        }

        return [
            'id' => $bookId,
            'title' => $title,
            'author' => ['name' => $authorName],
            'publisher' => $publisher,
            'description' => $summary,
            'chapters' => $chapters,
            'total_pages_estimate' => $totalPages,
            'total_volumes' => $totalVolumes
        ];
    }

    /**
     * Fetch a batch of pages in parallel using Http::pool.
     * Uses the "smart" page=X logic.
     *
     * @param string $bookId
     * @param int $startPage
     * @param int $endPage
     * @param int $volume Currently assumes pages are absolute or relative to volume? 
     *                    KetabOnline usually uses absolute page numbers across volumes OR relative.
     *                    Based on analysis: ?page=1&part=1. We need to respect the part.
     *                    Ideally, we should group fetching by Volume if pages restart, or just pass Volume.
     *                    For now, we'll try to determine the correct Part for the Page from known chapters.
     * @param array $chaptersMap To lookup which volume a page belongs to.
     * @return array
     */
    public function fetchPagesParallel(string $bookId, int $startPage, int $endPage, array $chaptersMap): array
    {
        $responses = Http::pool(function (Pool $pool) use ($bookId, $startPage, $endPage, $chaptersMap) {
            $requests = [];
            for ($page = $startPage; $page <= $endPage; $page++) {
                // Determine Volume for this Page
                // We find the chapter that has start_page <= current_page
                // This assumes page numbers are "Absolute Global" or we have a map.
                // KetabOnline links show specific ?part=X. 
                // We'll trust the Volume Mapping passed to us.
                
                $vol = $this->getVolumeForPage($page, $chaptersMap);
                
                $url = "{$this->baseUrl}/ar/books/{$bookId}/read?part={$vol}&page={$page}";
                $requests[] = $pool->as("page_{$page}")->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0'
                ])->get($url);
            }
            return $requests;
        });

        $results = [];
        foreach ($responses as $pageNumStr => $response) {
            $pageNum = (int)str_replace('page_', '', $pageNumStr);
            
            if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                $html = $response->body();
                $content = $this->extractContentFromHtml($html);
                
                if (!empty($content)) {
                    $results[$pageNum] = $content;
                } else {
                    Log::warning("KetabOnline: Empty content for Book {$bookId} Page {$pageNum}");
                }
            } else {
                Log::error("KetabOnline: Failed to fetch Page {$pageNum}");
            }
        }

        return $results;
    }

    /**
     * Extract pure text from the reading page HTML.
     */
    protected function extractContentFromHtml(string $html): string
    {
        $crawler = new Crawler($html);
        
        // Target .BookView and .g-paragraph
        // We want to preserve newlines.
        
        $paragraphs = $crawler->filter('.BookView .g-paragraph')->each(function (Crawler $node) {
            // Remove copy buttons or hidden elements if any
            // Scraper analysis said .g-copy buttons exist.
            // Text extraction usually ignores hidden elements but let's be safe.
            return trim($node->text());
        });

        return implode("\n\n", array_filter($paragraphs));
    }

    /**
     * Helper to find Volume for a given Page based on Chapter map.
     */
    protected function getVolumeForPage(int $page, array $chaptersMap): int
    {
        // chaptersMap should be sorted by start_page
        // We iterate and find the last chapter where start_page <= page
        $currentVol = 1;
        foreach ($chaptersMap as $chap) {
            if ($chap['start_page'] <= $page) {
                $currentVol = $chap['volume'];
            } else {
                break; // We passed the possible chapters for this page
            }
        }
        return $currentVol;
    }
}
