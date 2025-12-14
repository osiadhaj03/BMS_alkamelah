@props([
    'currentPage' => null,
    'pages' => collect(),
    'book' => null,
    'currentPageNum' => 1,
    'totalPages' => 1,
    'nextPage' => null,
    'previousPage' => null
])

<main class="flex-1 overflow-y-auto" style="height: calc(100vh - 80px); background-color: var(--bg-body);">
    <div class="max-w-4xl mx-auto p-8">
        <!-- Content Wrapper -->
        <div class="p-0">
            
            <!-- Page Content -->
            <div id="book-content-wrapper" class="space-y-8">
                @if($currentPage)
                    <div class="rounded-lg shadow-lg p-8 relative page-container transition-transform duration-300 hover:shadow-xl" 
                         style="background-color: var(--bg-paper); box-shadow: var(--shadow-paper); font-family: var(--font-main);"
                         data-page="{{ $currentPageNum }}">
                        
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                            <span class="text-xs text-gray-400" style="font-family: var(--font-ui);">
                                {{ $book?->title ?? 'الكتاب' }} - {{ $book?->authors?->first()?->full_name ?? 'المؤلف' }}
                            </span>
                            <span class="text-sm font-bold text-gray-300" style="font-family: var(--font-ui);">
                                صفحة {{ $currentPageNum }} من {{ $totalPages }}
                            </span>
                        </div>

                        <!-- Chapter Title if available -->
                        @if($currentPage->chapter)
                            <h3 class="text-xl font-bold mb-4" style="color: var(--accent-color); font-family: var(--font-ui);">
                                {{ $currentPage->chapter->title }}
                            </h3>
                        @endif

                        <!-- Content -->
                        <div class="prose prose-lg max-w-none leading-loose" style="color: var(--text-main); line-height: 2;">
                            {!! $currentPage->html_content ?? nl2br(e($currentPage->content)) !!}
                        </div>
                    </div>
                @else
                    <!-- No Content Message -->
                    <div class="rounded-lg shadow-lg p-8 text-center" 
                         style="background-color: var(--bg-paper); box-shadow: var(--shadow-paper);">
                        <div class="text-6xl mb-4">📖</div>
                        <p class="text-gray-500 text-lg" style="font-family: var(--font-ui);">
                            لم يتم العثور على محتوى لهذه الصفحة
                        </p>
                        <p class="text-gray-400 text-sm mt-2" style="font-family: var(--font-ui);">
                            حاول الانتقال إلى صفحة أخرى
                        </p>
                    </div>
                @endif
            </div>
            
            <!-- Navigation Buttons 
            <div class="flex items-center justify-between mt-8 gap-4">
                <-- Previous Page Button ->
                @if($previousPage) // 
                    <a href="{{ route('book.read', ['bookId' => $book?->id, 'pageNumber' => $previousPage->page_number]) }}" 
                       class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-xl shadow-md hover:bg-gray-50 hover:border-green-300 transition-all"
                       style="font-family: var(--font-ui);">
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>الصفحة السابقة</span>
                    </a>
                @else
                    <div class="px-6 py-3"></div>
                @endif
                
                <-- Page Jump ->
                <div class="flex items-center gap-2" style="font-family: var(--font-ui);">
                    <span class="text-sm text-gray-500">صفحة</span>
                    <input type="number" 
                           id="page-jump-input"
                           min="1" 
                           max="{{ $totalPages }}" 
                           value="{{ $currentPageNum }}"
                           class="w-16 px-2 py-2 border border-gray-200 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                           onchange="window.location.href='/book/{{ $book?->id }}/' + this.value">
                    <span class="text-sm text-gray-500">من {{ $totalPages }}</span>
                </div>
                
                <-- Next Page Button ->
                @if($nextPage)
                    <a href="{{ route('book.read', ['bookId' => $book?->id, 'pageNumber' => $nextPage->page_number]) }}" 
                       class="flex items-center gap-2 px-6 py-3 text-white rounded-xl shadow-md hover:opacity-90 transition-all"
                       style="background-color: var(--accent-color); font-family: var(--font-ui);">
                        <span>الصفحة التالية</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                @else
                    <div class="px-6 py-3"></div>
                @endif
            </div>
            -->
            <!-- Reading Controls (optional) -->
            {{-- <x-book.reading-controls /> --}}
        </div>
    </div>
</main>

<!-- Keyboard Navigation -->
<script>
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT') return;
        
        // Arrow Left/Up = Next Page (RTL)
        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            @if($nextPage)
                window.location.href = "{{ route('book.read', ['bookId' => $book?->id, 'pageNumber' => $nextPage->page_number]) }}";
            @endif
        }
        // Arrow Right/Down = Previous Page (RTL)
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            @if($previousPage)
                window.location.href = "{{ route('book.read', ['bookId' => $book?->id, 'pageNumber' => $previousPage->page_number]) }}";
            @endif
        }
    });
    
    // Highlight search query in content
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const highlightQuery = urlParams.get('highlight');
        
        if (highlightQuery && highlightQuery.length > 1) {
            const contentEl = document.querySelector('.prose');
            if (contentEl) {
                highlightText(contentEl, highlightQuery);
                
                // Scroll to first highlight
                setTimeout(() => {
                    const firstHighlight = document.querySelector('.search-highlight');
                    if (firstHighlight) {
                        firstHighlight.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 300);
            }
        }
    });
    
    function highlightText(element, query) {
        const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT, null, false);
        const textNodes = [];
        
        while (walker.nextNode()) {
            textNodes.push(walker.currentNode);
        }
        
        textNodes.forEach(node => {
            const text = node.textContent;
            const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
            
            if (regex.test(text)) {
                const span = document.createElement('span');
                span.innerHTML = text.replace(regex, '<mark class="search-highlight" style="background-color: #fef08a; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                node.parentNode.replaceChild(span, node);
            }
        });
    }
    
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
</script>