<div class="h-full flex flex-col" x-data>
    <!-- Results Header / Filter -->
    <div class="p-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
        <span class="text-xs font-bold text-gray-500">
            <span x-text="$store.search.totalResults || 0"></span> نتيجة
            <span x-show="$store.search.searchTime" class="text-gray-400">
                (خلال <span x-text="$store.search.searchTime"></span> مللي ثانية)
            </span>
        </span>
        
        <!-- Results Per Page Selector -->
        <div x-data="{ perPageOpen: false }" class="relative">
            <button @click="perPageOpen = !perPageOpen" class="flex items-center gap-1 text-xs text-gray-500 hover:text-green-600 transition-colors">
                <span x-text="$store.search.perPage"></span> نتائج
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="perPageOpen" @click.outside="perPageOpen = false" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute left-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10 py-1 min-w-[80px]">
                <button @click="$store.search.perPage = 10; $store.search.performSearch(); perPageOpen = false" 
                        class="w-full text-right px-3 py-1.5 text-xs hover:bg-gray-50"
                        :class="$store.search.perPage == 10 ? 'text-green-600 font-bold' : 'text-gray-600'">10</button>
                <button @click="$store.search.perPage = 15; $store.search.performSearch(); perPageOpen = false"
                        class="w-full text-right px-3 py-1.5 text-xs hover:bg-gray-50"
                        :class="$store.search.perPage == 15 ? 'text-green-600 font-bold' : 'text-gray-600'">15</button>
                <button @click="$store.search.perPage = 25; $store.search.performSearch(); perPageOpen = false"
                        class="w-full text-right px-3 py-1.5 text-xs hover:bg-gray-50"
                        :class="$store.search.perPage == 25 ? 'text-green-600 font-bold' : 'text-gray-600'">25</button>
                <button @click="$store.search.perPage = 50; $store.search.performSearch(); perPageOpen = false"
                        class="w-full text-right px-3 py-1.5 text-xs hover:bg-gray-50"
                        :class="$store.search.perPage == 50 ? 'text-green-600 font-bold' : 'text-gray-600'">50</button>
            </div>
        </div>
    </div>

    <!-- Scrollable List -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-2">
        
        <!-- Loading State -->
        <template x-if="$store.search.loading && $store.search.results.length === 0">
            <div class="py-12 text-center">
                <svg class="animate-spin h-8 w-8 mx-auto text-green-600 mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500 text-sm">جاري البحث...</p>
            </div>
        </template>
        
        <!-- Welcome State (No query) -->
        <template x-if="!$store.search.loading && $store.search.results.length === 0 && !$store.search.query">
            <div class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p class="text-gray-500 text-sm">ابدأ البحث لعرض النتائج</p>
            </div>
        </template>
        
        <!-- No Results State -->
        <template x-if="!$store.search.loading && $store.search.results.length === 0 && $store.search.query">
            <div class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600 font-medium">لا توجد نتائج</p>
                <p class="text-gray-400 text-sm mt-1">جرب كلمات مختلفة</p>
            </div>
        </template>
        
        <!-- Dynamic Results -->
        <template x-for="(result, index) in $store.search.results" :key="result.id || index">
            <div class="group relative bg-white hover:bg-gray-50 rounded-lg p-3 cursor-pointer border border-gray-100 hover:border-gray-300 transition-all"
                 @click="$store.search.selectResult(result)">
                 
                 <!-- Matched Terms (الكلمات المطابقة) - في الأعلى -->
                 <div x-show="result.matched_terms && result.matched_terms.length > 0" 
                      class="flex flex-wrap gap-1 mb-2">
                     <template x-for="term in result.matched_terms" :key="term">
                         <span class="inline-flex items-center gap-1 text-[10px] bg-green-50 text-green-700 px-2 py-0.5 rounded-full border border-green-200 font-medium">
                             <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                 <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                             </svg>
                             <span x-text="term"></span>
                         </span>
                     </template>
                 </div>
                 
                 <div class="flex justify-between items-start mb-1">
                     <h4 class="font-bold text text-gray-800 line-clamp-1" x-text="result.book_title || result.title || 'بدون عنوان'"></h4>
                     <span class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded text-gray-500 whitespace-nowrap">
                         صـ <span x-text="result.page_number || '-'"></span>
                     </span>
                 </div>
                 
                 <p class="text-xs text-gray-500 mb-1.5" x-text="result.author_name || 'غير محدد'"></p>
                 
                 <div class="text-xs text-gray-600 leading-relaxed font-serif line-clamp-2" x-html="result.highlighted_content || result.content || ''"></div>
            </div>
        </template>

        <!-- Load More Button -->
        <template x-if="$store.search.hasMore && $store.search.results.length > 0">
            <div class="py-3 text-center">
                <button @click="$store.search.loadMore()" 
                        :disabled="$store.search.loading"
                        class="w-full py-2.5 px-4 text-sm font-bold text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg border border-green-200 hover:border-green-300 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <template x-if="$store.search.loading">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!$store.search.loading">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </template>
                    <span x-text="$store.search.loading ? 'جاري التحميل...' : 'عرض المزيد من النتائج'"></span>
                </button>
            </div>
        </template>

    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 20px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #d1d5db;
    }
</style>
