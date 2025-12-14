<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Page;
use App\Models\Chapter;
use Illuminate\Http\Request;

class BookReaderController extends Controller
{
    /**
     * Step 1: Basic Book Display - Load real data from database
     * 
     * @param int $bookId
     * @param int|null $pageNumber
     */
    public function show($bookId, $pageNumber = 1)
    {
        // Load book with relationships
        $book = Book::with([
            'authors' => fn($q) => $q->orderByPivot('display_order'),
            'bookSection',
            'volumes' => fn($q) => $q->orderBy('number'),
        ])->findOrFail($bookId);

        // Load current page
        $currentPage = Page::where('book_id', $bookId)
            ->where('page_number', $pageNumber)
            ->with(['chapter', 'volume'])
            ->first();

        // If page not found, get first page
        if (!$currentPage) {
            $currentPage = Page::where('book_id', $bookId)
                ->orderBy('page_number')
                ->first();
            
            if ($currentPage) {
                $pageNumber = $currentPage->page_number;
            }
        }

        // Load chapters for TOC (root chapters with ALL nested children)
        $chapters = Chapter::where('book_id', $bookId)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->with('allChildren')
            ->get();

        // Get total pages count
        $totalPages = Page::where('book_id', $bookId)->count();

        // Get navigation info
        $previousPage = $currentPage ? Page::where('book_id', $bookId)
            ->where('page_number', '<', $pageNumber)
            ->orderByDesc('page_number')
            ->first() : null;

        $nextPage = $currentPage ? Page::where('book_id', $bookId)
            ->where('page_number', '>', $pageNumber)
            ->orderBy('page_number')
            ->first() : null;

        // Get pages for display (current page only for now)
        $pages = $currentPage ? collect([$currentPage]) : collect();

        $currentPageNum = $pageNumber;

        return view('pages.book-preview', compact(
            'book',
            'pages',
            'chapters',
            'currentPage',
            'currentPageNum',
            'totalPages',
            'nextPage',
            'previousPage'
        ));
    }

    /**
     * Search within a specific book
     * 
     * @param Request $request
     * @param int $bookId
     */
    public function search(Request $request, $bookId)
    {
        $query = $request->input('q', '');
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 10);
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'total' => 0,
                'hasMore' => false,
                'message' => 'الرجاء إدخال كلمة بحث (حرفين على الأقل)'
            ]);
        }

        // Remove Arabic diacritics for better matching
        $cleanQuery = $this->removeArabicDiacritics($query);
        
        // Build base query
        $baseQuery = Page::where('book_id', $bookId)
            ->where(function($q) use ($query, $cleanQuery) {
                // Search with original query (with diacritics)
                $q->where('content', 'LIKE', "%{$query}%")
                  ->orWhere('html_content', 'LIKE', "%{$query}%");
                
                // Also search without diacritics if different
                if ($cleanQuery !== $query) {
                    $q->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(content, 'ً', ''), 'ٌ', ''), 'ٍ', ''), 'َ', ''), 'ُ', ''), 'ِ', ''), 'ّ', ''), 'ْ', '') LIKE ?", ["%{$cleanQuery}%"]);
                }
            });
        
        // Get total count
        $total = $baseQuery->count();
        
        // Get paginated results
        $results = (clone $baseQuery)
            ->with('chapter:id,title')
            ->select('id', 'page_number', 'chapter_id', 'content', 'html_content')
            ->orderBy('page_number')
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Format results with snippets
        $formattedResults = $results->map(function($page) use ($query) {
            $content = strip_tags($page->html_content ?? $page->content);
            $snippet = $this->getSearchSnippet($content, $query, 150);
            
            return [
                'page_number' => $page->page_number,
                'chapter' => $page->chapter?->title,
                'snippet' => $snippet,
            ];
        });

        return response()->json([
            'results' => $formattedResults,
            'total' => $total,
            'offset' => $offset,
            'hasMore' => ($offset + $limit) < $total,
            'query' => $query
        ]);
    }

    /**
     * Remove Arabic diacritics (harakat) from text
     */
    private function removeArabicDiacritics($text)
    {
        // Arabic diacritics: Fathatan, Dammatan, Kasratan, Fatha, Damma, Kasra, Shadda, Sukun
        $diacritics = ['ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ', 'ٰ'];
        return str_replace($diacritics, '', $text);
    }

    /**
     * Get a snippet of text around the search query
     */
    private function getSearchSnippet($content, $query, $length = 150)
    {
        $content = strip_tags($content);
        $position = mb_stripos($content, $query);
        
        if ($position === false) {
            // Try without diacritics
            $cleanContent = $this->removeArabicDiacritics($content);
            $cleanQuery = $this->removeArabicDiacritics($query);
            $position = mb_stripos($cleanContent, $cleanQuery);
        }
        
        if ($position === false) {
            return mb_substr($content, 0, $length) . '...';
        }

        $start = max(0, $position - 50);
        $snippet = mb_substr($content, $start, $length);
        
        // Add ellipsis
        if ($start > 0) {
            $snippet = '...' . $snippet;
        }
        if (mb_strlen($content) > $start + $length) {
            $snippet .= '...';
        }

        // Highlight the query in the snippet
        $snippet = preg_replace(
            '/(' . preg_quote($query, '/') . ')/iu',
            '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>',
            $snippet
        );

        return $snippet;
    }
}