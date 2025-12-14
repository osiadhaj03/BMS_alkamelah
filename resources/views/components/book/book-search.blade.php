@props([
    'book' => null
])

<div class="book-search-container" x-data="bookSearch({{ $book?->id ?? 'null' }})">
    <!-- Search Input -->
    <div class="relative">
        <input type="text" 
               x-model="query"
               @input.debounce.500ms="search(true)"
               @keydown.escape="clearSearch()"
               placeholder="ابحث في الكتاب ..."
               class="w-full p-3 pl-10 rounded-xl border text-sm transition-all focus:ring-2 focus:ring-green-500"
               style="border-color: var(--border-color); font-family: var(--font-ui); background-color: var(--bg-paper); color: var(--text-main);">
        
        <!-- Search Icon -->
        <div class="absolute top-3 left-3 text-gray-400">
            <template x-if="!loading">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </template>
            <template x-if="loading">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </template>
        </div>
        
        <!-- Clear Button -->
        <button x-show="query.length > 0" 
                @click="clearSearch()"
                class="absolute top-2.5 right-3 p-1 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
            
            <!--
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            -->
            
        </button>
    </div>
    
    <!-- Results Dropdown -->
    <div x-show="showResults" 
         x-transition
         class="mt-2 bg-white rounded-xl shadow-xl border overflow-hidden z-50"
         style="max-height: 350px; border-color: var(--border-color); background-color: var(--bg-paper);">
        
        <!-- Results List -->
        <div class="overflow-y-auto" style="max-height: 400px;">
            <!-- No Results -->
            <template x-if="results.length === 0 && !loading && searched">
                <div class="p-6 text-center">
                    <div class="text-4xl mb-2">🔍</div>
                    <p class="text-gray-500">لا توجد نتائج للبحث</p>
                </div>
            </template>
            
            <!-- Results Items -->
            <template x-for="result in results" :key="result.page_number">
                <a :href="'/book/{{ $book?->id }}/' + result.page_number + '?highlight=' + encodeURIComponent(query)"
                   class="block p-4 hover:bg-gray-50 border-b transition-colors"
                   style="border-color: var(--border-color);">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                            صفحة <span x-text="result.page_number"></span>
                        </span>
                        <span x-show="result.chapter" 
                              class="text-xs text-gray-500 truncate max-w-[150px]"
                              x-text="result.chapter"></span>
                    </div>
                    <p class="search-result-text text-gray-700 mt-2" 
                       x-html="result.snippet"
                       style="font-family: var(--font-arabic); font-size: 1rem; line-height: 1.35rem;"></p>
                </a>
            </template>
            
            <!-- Load More Button -->
            <div x-show="hasMore && !loading" class="p-3 text-center border-t" style="border-color: var(--border-color);">
                <button @click="loadMore()" 
                        class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                    <span>عرض المزيد</span>
                    <span class="text-gray-500 mr-1">(<span x-text="results.length"></span> من <span x-text="total"></span>)</span>
                </button>
            </div>
            
            <!-- Loading More -->
            <div x-show="loadingMore" class="p-3 text-center">
                <svg class="w-5 h-5 animate-spin mx-auto text-green-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<script>
function bookSearch(bookId) {
    return {
        bookId: bookId,
        query: '',
        results: [],
        total: 0,
        offset: 0,
        hasMore: false,
        loading: false,
        loadingMore: false,
        showResults: false,
        searched: false,
        
        async search(reset = true) {
            if (this.query.length < 2) {
                this.results = [];
                this.total = 0;
                this.offset = 0;
                this.hasMore = false;
                this.showResults = false;
                this.searched = false;
                return;
            }
            
            if (reset) {
                this.results = [];
                this.offset = 0;
                this.loading = true;
            }
            
            this.searched = true;
            
            try {
                const response = await fetch(`/book/${this.bookId}/search?q=${encodeURIComponent(this.query)}&offset=${this.offset}&limit=10`);
                const data = await response.json();
                
                if (reset) {
                    this.results = data.results || [];
                } else {
                    this.results = [...this.results, ...(data.results || [])];
                }
                
                this.total = data.total || 0;
                this.hasMore = data.hasMore || false;
                this.showResults = true;
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.loading = false;
                this.loadingMore = false;
            }
        },
        
        async loadMore() {
            this.loadingMore = true;
            this.offset += 10;
            await this.search(false);
        },
        
        clearSearch() {
            this.query = '';
            this.results = [];
            this.total = 0;
            this.offset = 0;
            this.hasMore = false;
            this.showResults = false;
            this.searched = false;
        }
    }
}
</script>
