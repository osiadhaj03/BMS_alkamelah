<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Page;
use App\Models\Chapter;
use Illuminate\Http\Request;

class BookEditorController extends Controller
{
    /**
     * Display the book editor page
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

        // Get a map of sequential page numbers to original page numbers for the TOC
        $originalPageMap = Page::where('book_id', $bookId)
            ->whereNotNull('original_page_number')
            ->pluck('original_page_number', 'page_number')
            ->toArray();

        return view('pages.book-editor', compact(
            'book',
            'pages',
            'chapters',
            'currentPage',
            'currentPageNum',
            'totalPages',
            'nextPage',
            'previousPage',
            'originalPageMap'
        ));
    }

    /**
     * Update page content via AJAX
     * 
     * @param Request $request
     * @param int $bookId
     * @param int $pageNumber
     */
    public function updatePage(Request $request, $bookId, $pageNumber)
    {
        $request->validate([
            'content' => 'required|string',
            'html_content' => 'nullable|string',
        ]);

        $page = Page::where('book_id', $bookId)
            ->where('page_number', $pageNumber)
            ->firstOrFail();

        $page->update([
            'content' => strip_tags($request->input('content')),
            'html_content' => $request->input('html_content', $request->input('content')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الصفحة بنجاح',
            'page_number' => $page->page_number,
        ]);
    }

    /**
     * Insert a new blank page before the current page
     * 
     * @param Request $request
     * @param int $bookId
     * @param int $pageNumber
     */
    public function insertPageBefore(Request $request, $bookId, $pageNumber)
    {
        $book = Book::findOrFail($bookId);
        
        // 1. Increment page_number for all pages >= pageNumber
        Page::where('book_id', $bookId)
            ->where('page_number', '>=', $pageNumber)
            ->increment('page_number');
        
        // 2. Update chapter ranges
        Chapter::where('book_id', $bookId)
            ->where('page_start', '>=', $pageNumber)
            ->increment('page_start');
        
        Chapter::where('book_id', $bookId)
            ->where('page_end', '>=', $pageNumber)
            ->increment('page_end');
        
        // 3. Create new blank page at pageNumber
        $newPage = Page::create([
            'book_id' => $bookId,
            'page_number' => $pageNumber,
            'content' => '',
            'html_content' => '<p></p>',
        ]);
        
        // 4. Update total pages count
        $totalPages = Page::where('book_id', $bookId)->count();
        $book->update(['total_pages' => $totalPages]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم إدراج صفحة جديدة قبل الصفحة الحالية',
            'page' => $newPage,
            'new_page_number' => $pageNumber,
        ]);
    }

    /**
     * Insert a new blank page after the current page
     * 
     * @param Request $request
     * @param int $bookId
     * @param int $pageNumber
     */
    public function insertPageAfter(Request $request, $bookId, $pageNumber)
    {
        $book = Book::findOrFail($bookId);
        $newPageNumber = $pageNumber + 1;
        
        // 1. Increment page_number for all pages > pageNumber
        Page::where('book_id', $bookId)
            ->where('page_number', '>=', $newPageNumber)
            ->increment('page_number');
        
        // 2. Update chapter ranges
        Chapter::where('book_id', $bookId)
            ->where('page_start', '>=', $newPageNumber)
            ->increment('page_start');
        
        Chapter::where('book_id', $bookId)
            ->where('page_end', '>=', $newPageNumber)
            ->increment('page_end');
        
        // 3. Create new blank page at newPageNumber
        $newPage = Page::create([
            'book_id' => $bookId,
            'page_number' => $newPageNumber,
            'content' => '',
            'html_content' => '<p></p>',
        ]);
        
        // 4. Update total pages count
        $totalPages = Page::where('book_id', $bookId)->count();
        $book->update(['total_pages' => $totalPages]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم إدراج صفحة جديدة بعد الصفحة الحالية',
            'page' => $newPage,
            'new_page_number' => $newPageNumber,
        ]);
    }
}
