@extends('layouts.app')

@section('content')
    <div class="flex flex-col h-screen bg-gray-50" dir="rtl" x-data x-init="$store.search.init()">
        <!-- Global Header -->
        <div class="z-50 relative">
            <x-layout.header />
        </div>

        <!-- Search Header Section -->
        <div class="flex-none z-40 bg-white border-b border-gray-200 shadow-sm relative">
            <x-search.header />
        </div>
        
        <!-- Persistent Maintenance Alert 
        <div class="bg-yellow-50 border-b border-yellow-200 p-4 relative z-50">
            <div class="flex items-center justify-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-yellow-800">
                        تنبيه: البحث حالياً لا يعمل بسبب أعمال التحديث الجارية على الموقع. نعتذر عن الإزعاج و نشكر تفهمكم.
                    </p>
                </div>
            </div>
        </div>
        -->

        <!-- Main Content Area (Sidebar + Preview) -->
        <div class="flex flex-1 overflow-hidden relative z-0">

            <!-- Right Sidebar (Search Results) -->
            <aside
                class="w-80 md:w-96 flex-none bg-white border-l border-gray-200 shadow-lg z-10 hidden lg:block overflow-hidden relative"
                id="search-sidebar">
                <x-search.results-sidebar />
            </aside>

            <!-- Main Preview Pane -->
            <main class="flex-1 overflow-hidden relative bg-gray-100 flex flex-col justify-center items-center">
                <x-search.preview-pane />
            </main>

            <!-- Mobile Toggle Button (Visible only on small screens) -->
            <button id="mobile-sidebar-toggle" @click="$store.search.mobileSearchOpen = true"
                class="lg:hidden absolute bottom-10 right-6 z-30 p-3 rounded-full bg-green-600 text-white shadow-lg hover:bg-green-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>

        {{-- Mobile Sidebar Overlay --}}
        <div x-show="$store.search.mobileSearchOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$store.search.mobileSearchOpen = false"
         class="fixed inset-0 bg-black/50 z-[100] lg:hidden"
         style="display: none;">
        <div @click.stop
             x-show="$store.search.mobileSearchOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="absolute inset-y-0 right-0 w-80 bg-white">
            <div class="h-full flex flex-col">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-bold">نتائج البحث</h3>
                    <button @click="$store.search.mobileSearchOpen = false" 
                            class="p-2 text-gray-500 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-hidden relative">
                    <x-search.results-sidebar />
                </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                // Create a global store for search state
                Alpine.store('search', {
                    // Search State
                    query: '',
                    searchType: 'exact_match',
                    wordOrder: 'consecutive',
                    wordMatch: 'all_words',
                    sortBy: 'relevance',
                    perPage: 10,
                    page: 1,

                    // Results State
                    results: [],
                    totalResults: 0,
                    loading: false,
                    hasMore: true,
                    searchTime: 0,

                    // Filters
                    selectedFilters: { books: [], authors: [], sections: [] },

                    // Mobile
                    mobileSearchOpen: false,

                    // Selected result for preview
                    selectedResult: null,

                    // Init from URL Params
                    init() {
                        const params = new URLSearchParams(window.location.search);
                        if (params.has('q')) {
                            this.query = params.get('q');
                            this.searchType = params.get('search_type') || 'exact_match';
                            this.wordOrder = params.get('word_order') || 'consecutive';
                            this.wordMatch = params.get('word_match') || 'all_words';

                            // Handle filters if present
                            if (params.has('book_id')) this.selectedFilters.books = params.get('book_id').split(',');
                            if (params.has('author_id')) this.selectedFilters.authors = params.get('author_id').split(',');
                            if (params.has('section_id')) this.selectedFilters.sections = params.get('section_id').split(',');

                            this.performSearch();
                        }
                    },

                    // Perform Search
                    async performSearch(resetPage = true) {
                        if (!this.query.trim()) {
                            this.results = [];
                            this.totalResults = 0;
                            return;
                        }

                        if (resetPage) {
                            this.page = 1;
                            this.results = [];
                        }

                        this.loading = true;
                        const startTime = performance.now();

                        try {
                            const params = new URLSearchParams({
                                q: this.query,
                                per_page: this.perPage,
                                page: this.page,
                                search_type: this.searchType,
                                word_order: this.wordOrder,
                                word_match: this.wordMatch,
                                sort_by: this.sortBy
                            });

                            // Add filters
                            if (this.selectedFilters.books.length > 0) {
                                params.append('book_id', this.selectedFilters.books.join(','));
                            }
                            if (this.selectedFilters.authors.length > 0) {
                                params.append('author_id', this.selectedFilters.authors.join(','));
                            }
                            if (this.selectedFilters.sections.length > 0) {
                                params.append('section_id', this.selectedFilters.sections.join(','));
                            }

                            console.log('Searching:', this.query, params.toString());

                            const response = await fetch('/api/ultra-search?' + params.toString());
                            const data = await response.json();

                            console.log('Search response:', data);
                            if (data.data && data.data.length > 0) {
                                console.log('First result:', data.data[0]);
                                console.log('First result matched_terms:', data.data[0].matched_terms);
                                console.log('First result author_name:', data.data[0].author_name);
                            }

                            this.searchTime = Math.round(performance.now() - startTime);

                            if (data.success && data.data) {
                                if (resetPage) {
                                    this.results = data.data;
                                } else {
                                    this.results = [...this.results, ...data.data];
                                }
                                this.totalResults = data.pagination?.total || data.data.length;
                                this.hasMore = data.pagination ? (this.page < data.pagination.last_page) : false;
                            }
                        } catch (error) {
                            console.error('Search error:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    // Load More Results
                    async loadMore() {
                        if (!this.hasMore || this.loading) return;
                        this.page++;
                        await this.performSearch(false);
                    },

                    // Select a result and load full page content
                    selectedResult: null,
                    loadingPreview: false,

                    async selectResult(result) {
                        if (!result || !result.id) {
                            this.selectedResult = result;
                            return;
                        }

                        this.loadingPreview = true;

                        try {
                            // Fetch full page content with highlight
                            const response = await fetch(`/api/page/${result.id}?q=${encodeURIComponent(this.query)}`);
                            const data = await response.json();

                            if (data.success && data.data) {
                                this.selectedResult = {
                                    ...result,
                                    ...data.data,
                                    highlighted_content: data.data.content
                                };
                            } else {
                                // Fallback to original result
                                this.selectedResult = result;
                            }
                        } catch (error) {
                            console.error('Failed to load full page:', error);
                            this.selectedResult = result;
                        } finally {
                            this.loadingPreview = false;
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection