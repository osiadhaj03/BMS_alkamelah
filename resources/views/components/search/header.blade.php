<div class="px-4 py-3 md:px-6 md:py-4 bg-white" x-data="{ 
    settingsOpen: false, 
    searchType: 'flexible_match',
    wordOrder: 'any_order',
    query: '',
    filterModalOpen: false,
    activeTab: 'books',
    filterSearch: '',
    
    // Dynamic Books Data
    books: [],
    booksPage: 1,
    hasMoreBooks: true,
    booksLoading: false,
    booksSearchTimeout: null,
    
    // Dynamic Authors Data
    authors: [],
    authorsPage: 1,
    hasMoreAuthors: true,
    authorsLoading: false,
    authorsSearchTimeout: null,
    
    // Dynamic Sections Data (load all at once)
    sections: [],
    sectionsLoading: false,
    sectionsLoaded: false,
    
    sortOpen: false,
    sortBy: 'relevance',
    mobileMenuOpen: false,

    // Load books from API
    async loadBooks(page = 1, append = false) {
        this.booksLoading = true;
        try {
            const params = new URLSearchParams({
                page: page,
                search: this.filterSearch
            });
            const response = await fetch('/api/books?' + params.toString());
            const data = await response.json();
            
            if (append) {
                this.books = [...this.books, ...data.data.map(b => ({ id: b.id, name: b.title }))];
            } else {
                this.books = data.data.map(b => ({ id: b.id, name: b.title }));
            }
            
            this.booksPage = data.current_page;
            this.hasMoreBooks = data.current_page < data.last_page;
        } catch (error) {
            console.error('Failed to load books:', error);
        } finally {
            this.booksLoading = false;
        }
    },

    // Search books with debounce
    searchBooks() {
        clearTimeout(this.booksSearchTimeout);
        this.booksSearchTimeout = setTimeout(() => {
            this.booksPage = 1;
            this.loadBooks(1, false);
        }, 300);
    },

    // Load more books
    loadMoreBooks() {
        if (this.hasMoreBooks && !this.booksLoading) {
            this.loadBooks(this.booksPage + 1, true);
        }
    },

    // Load authors from API
    async loadAuthors(page = 1, append = false) {
        this.authorsLoading = true;
        try {
            const params = new URLSearchParams({
                page: page,
                search: this.filterSearch
            });
            const response = await fetch('/api/authors?' + params.toString());
            const data = await response.json();
            
            if (append) {
                this.authors = [...this.authors, ...data.data];
            } else {
                this.authors = data.data;
            }
            
            this.authorsPage = data.current_page;
            this.hasMoreAuthors = data.current_page < data.last_page;
        } catch (error) {
            console.error('Failed to load authors:', error);
        } finally {
            this.authorsLoading = false;
        }
    },

    // Search authors with debounce
    searchAuthors() {
        clearTimeout(this.authorsSearchTimeout);
        this.authorsSearchTimeout = setTimeout(() => {
            this.authorsPage = 1;
            this.loadAuthors(1, false);
        }, 300);
    },

    // Load more authors
    loadMoreAuthors() {
        if (this.hasMoreAuthors && !this.authorsLoading) {
            this.loadAuthors(this.authorsPage + 1, true);
        }
    },

    // Load all sections at once
    async loadSections() {
        if (this.sectionsLoaded) return;
        this.sectionsLoading = true;
        try {
            const response = await fetch('/api/sections');
            this.sections = await response.json();
            this.sectionsLoaded = true;
        } catch (error) {
            console.error('Failed to load sections:', error);
        } finally {
            this.sectionsLoading = false;
        }
    },

    // Filter sections locally (since all are loaded)
    get filteredSections() {
        if (!this.filterSearch) return this.sections;
        return this.sections.filter(s => s.name.includes(this.filterSearch));
    },

    // Init - load data when modal opens
    initFilter() {
        if (this.books.length === 0) {
            this.loadBooks();
        }
        if (this.authors.length === 0) {
            this.loadAuthors();
        }
        if (!this.sectionsLoaded) {
            this.loadSections();
        }
    }
}" style="background-image: url('{{ asset('images/backgrond_islamic.png') }}'); background-position: center;">
    <div class="max-w-7xl mx-auto flex flex-col gap-4">

        <!-- Top Row: Logo & Main Search Input -->
        <div class="flex items-center gap-4">

            <!-- Search Bar Container -->
            <div class="flex-1 relative">
                <div class="relative flex items-center bg-gray-100 border border-gray-300 rounded-xl z-30">

                    <!-- Search Icon -->
                    <div class="pl-3 pr-4 text-gray-500">
                    </div>

                    <!-- Input -->
                    <input type="text" x-model="$store.search.query" @keydown.enter="$store.search.performSearch()"
                        class="w-full bg-transparent border-none py-3 text-lg text-gray-800 placeholder-gray-400 focus:ring-0"
                        placeholder="ابحث عن كلمة، عبارة، أو موضوع..." autofocus>

                    <!-- Mobile Menu Button (Visible only on mobile) -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" @click.outside="mobileMenuOpen = false"
                        class="md:hidden px-3 text-gray-600 hover:text-green-700 border-l border-gray-300 flex items-center gap-2 transition-colors h-[28px]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 14a1 1 0 110-2 1 1 0 010 2z">
                            </path>
                        </svg>
                    </button>

                    <!-- Settings Toggle Button (Desktop) -->
                    <button @click="settingsOpen = !settingsOpen" @click.outside="settingsOpen = false"
                        class="hidden md:flex px-4 py-2 text-gray-600 hover:text-green-700 hover:bg-gray-200 border-r border-gray-300 items-center gap-2 transition-colors relative h-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': settingsOpen}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <!-- Search Button -->
                    <button @click="$store.search.performSearch()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 md:px-6 py-3 rounded-l-xl font-medium transition-colors">
                        <span class="hidden md:inline" x-show="!$store.search.loading">بحث</span>
                        <svg x-show="$store.search.loading" class="animate-spin w-5 h-5" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg x-show="!$store.search.loading" class="w-5 h-5 md:hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Menu Dropdown -->
                <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2" @click.outside="mobileMenuOpen = false"
                    class="absolute top-full right-0 mt-2 w-full bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden md:hidden">

                    <div class="p-2 space-y-1">
                        <!-- Filter Logic in Menu -->
                        <button @click="filterModalOpen = true; mobileMenuOpen = false"
                            class="w-full flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg group text-right">
                            <div
                                class="p-1.5 rounded-md bg-gray-100 text-gray-500 group-hover:bg-green-100 group-hover:text-green-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                    </path>
                                </svg>
                            </div>
                            <span class="font-medium">تصفية النتائج</span>
                        </button>

                        <!-- Sort Options in Menu -->
                        <div x-data="{ expandedSort: false }" class="border-t border-b border-gray-50 my-1">
                            <button @click="expandedSort = !expandedSort"
                                class="w-full flex items-center justify-between px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg group text-right">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-1.5 rounded-md bg-gray-100 text-gray-500 group-hover:bg-green-100 group-hover:text-green-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">ترتيب النتائج</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition-transform"
                                    :class="{'rotate-180': expandedSort}" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="expandedSort" class="bg-gray-50 px-2 py-1 space-y-1">
                                <button @click="sortBy = 'relevance'; mobileMenuOpen = false"
                                    class="w-full text-right px-8 py-2 text-sm rounded hover:bg-white"
                                    :class="sortBy === 'relevance' ? 'text-green-600 font-bold' : 'text-gray-600'">أقرب
                                    صلة</button>
                                <button @click="sortBy = 'death_year_asc'; mobileMenuOpen = false"
                                    class="w-full text-right px-8 py-2 text-sm rounded hover:bg-white"
                                    :class="sortBy === 'death_year_asc' ? 'text-green-600 font-bold' : 'text-gray-600'">سنة
                                    الوفاة أولا</button>
                                <button @click="sortBy = 'death_year_desc'; mobileMenuOpen = false"
                                    class="w-full text-right px-8 py-2 text-sm rounded hover:bg-white"
                                    :class="sortBy === 'death_year_desc' ? 'text-green-600 font-bold' : 'text-gray-600'">سنة
                                    الوفاة الاحدث أولا</button>
                                <button @click="sortBy = 'alphabet'; mobileMenuOpen = false"
                                    class="w-full text-right px-8 py-2 text-sm rounded hover:bg-white"
                                    :class="sortBy === 'alphabet' ? 'text-green-600 font-bold' : 'text-gray-600'">اسم
                                    الكتاب أبجديا</button>
                            </div>
                        </div>

                        <!-- Search Settings Trigger -->
                        <button @click="settingsOpen = !settingsOpen; mobileMenuOpen = false"
                            class="w-full flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg group text-right">
                            <div
                                class="p-1.5 rounded-md bg-gray-100 text-gray-500 group-hover:bg-green-100 group-hover:text-green-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="font-medium">إعدادات البحث</span>
                        </button>

                        <!-- Help -->
                        <button
                            class="w-full flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg group text-right">
                            <div
                                class="p-1.5 rounded-md bg-gray-100 text-gray-500 group-hover:bg-green-100 group-hover:text-green-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <span class="font-medium">مساعدة</span>
                        </button>
                    </div>
                </div>



                <!-- Settings Dropdown Menu (Floating) -->
                <div x-show="settingsOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2" @click.outside="settingsOpen = false"
                    class="absolute top-full left-0 mt-2 w-full md:w-2/3 lg:w-[400px] bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden"
                    style="left: 0;">

                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Search Type -->
                        <div class="space-y-3">
                            <h4
                                class="font-bold text-gray-700 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">
                                نوع البحث
                            </h4>
                            <div class="flex flex-col gap-1">
                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <div class="relative flex items-center">
                                        <input type="radio" name="searchType" value="exact_match"
                                            x-model="$store.search.searchType"
                                            class="peer h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    </div>
                                    <span class="text-sm text-gray-700 group-hover:text-green-700 font-medium">البحث
                                        المطابق</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="searchType" value="flexible_match"
                                        x-model="$store.search.searchType"
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-green-700 font-medium">البحث
                                        المرن</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="searchType" value="morphological"
                                        x-model="$store.search.searchType"
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-green-700 font-medium">البحث
                                        الصرفي</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="searchType" value="fuzzy"
                                        x-model="$store.search.searchType"
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-green-700 font-medium">تصحيح
                                        الأخطاء</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="searchType" value="prefix"
                                        x-model="$store.search.searchType"
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-green-700 font-medium">البحث
                                        بالبداية</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="searchType" value="wildcard"
                                        x-model="$store.search.searchType"
                                        class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-orange-700 font-medium">الرموز
                                        البديلة <span class="text-xs text-gray-400">(*,?)</span></span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="searchType" value="boolean"
                                        x-model="$store.search.searchType"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-blue-700 font-medium">البحث
                                        المتقدم <span class="text-xs text-gray-400">(AND,OR,NOT)</span></span>
                                </label>

                            </div>
                        </div>

                        <!-- Word Order -->
                        <div class="space-y-3">
                            <h4
                                class="font-bold text-gray-700 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">
                                ترتيب الكلمات
                            </h4>
                            <div class="flex flex-col gap-1">
                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="wordOrder" value="consecutive"
                                        x-model="$store.search.wordOrder" @change="$store.search.performSearch()"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-blue-700 font-medium">كلمات
                                        متتالية</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="wordOrder" value="same_paragraph"
                                        x-model="$store.search.wordOrder" @change="$store.search.performSearch()"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-blue-700 font-medium">في نفس
                                        الفقرة</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="wordOrder" value="any_order"
                                        x-model="$store.search.wordOrder" @change="$store.search.performSearch()"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-blue-700 font-medium">أي
                                        ترتيب</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Word Match Section -->
                    <div class="px-4 pb-4">
                        <div class="space-y-3">
                            <h4
                                class="font-bold text-gray-700 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">
                                شرط الكلمات
                            </h4>
                            <div class="flex flex-col gap-1">
                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="wordMatch" value="all_words"
                                        x-model="$store.search.wordMatch" @change="$store.search.performSearch()"
                                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-purple-700 font-medium">كل
                                        الكلمات (AND)</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="wordMatch" value="some_words"
                                        x-model="$store.search.wordMatch" @change="$store.search.performSearch()"
                                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-purple-700 font-medium">بعض
                                        الكلمات (OR)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown Footer -->
                    <div
                        class="bg-gray-50 px-4 py-2 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-400">
                        <button @click="settingsOpen = false"
                            class="text-gray-500 hover:text-gray-700 font-bold">إغلاق</button>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Group (Filter / Sort / Help) - Hidden on Mobile -->
            <div class="hidden md:flex items-center gap-2">
                <button @click="filterModalOpen = true"
                    class="p-2.5 text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors"
                    title="تصفية النتائج">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                </button>
                <div class="relative">
                    <button @click="sortOpen = !sortOpen" @click.outside="sortOpen = false"
                        class="p-2.5 text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-200"
                        :class="{'bg-gray-100 text-green-600 border-green-200': sortOpen}" title="ترتيب النتائج">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                        </svg>
                    </button>

                    <!-- Sort Dropdown -->
                    <div x-show="sortOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="absolute top-full left-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden"
                        style="display: none;">
                        <div class="py-1">
                            <button @click="$store.search.sortBy = 'relevance'; sortOpen = false; $store.search.performSearch()"
                                class="w-full text-right px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center justify-between group"
                                :class="$store.search.sortBy === 'relevance' ? 'text-green-600 bg-green-50' : 'text-gray-700'">
                                <span>أقرب صلة</span>
                                <svg x-show="$store.search.sortBy === 'relevance'" class="w-4 h-4 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>

                            <button @click="$store.search.sortBy = 'book_title_asc'; sortOpen = false; $store.search.performSearch()"
                                class="w-full text-right px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center justify-between group"
                                :class="$store.search.sortBy === 'book_title_asc' ? 'text-green-600 bg-green-50' : 'text-gray-700'">
                                <span>اسم الكتاب (أ-ي)</span>
                                <svg x-show="$store.search.sortBy === 'book_title_asc'" class="w-4 h-4 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>

                            <button @click="$store.search.sortBy = 'book_title_desc'; sortOpen = false; $store.search.performSearch()"
                                class="w-full text-right px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center justify-between group"
                                :class="$store.search.sortBy === 'book_title_desc' ? 'text-green-600 bg-green-50' : 'text-gray-700'">
                                <span>اسم الكتاب (ي-أ)</span>
                                <svg x-show="$store.search.sortBy === 'book_title_desc'" class="w-4 h-4 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>

                            <button @click="$store.search.sortBy = 'page_number_asc'; sortOpen = false; $store.search.performSearch()"
                                class="w-full text-right px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center justify-between group"
                                :class="$store.search.sortBy === 'page_number_asc' ? 'text-green-600 bg-green-50' : 'text-gray-700'">
                                <span>رقم الصفحة (تصاعدي)</span>
                                <svg x-show="$store.search.sortBy === 'page_number_asc'" class="w-4 h-4 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>

                            <button @click="$store.search.sortBy = 'page_number_desc'; sortOpen = false; $store.search.performSearch()"
                                class="w-full text-right px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center justify-between group"
                                :class="$store.search.sortBy === 'page_number_desc' ? 'text-green-600 bg-green-50' : 'text-gray-700'">
                                <span>رقم الصفحة (تنازلي)</span>
                                <svg x-show="$store.search.sortBy === 'page_number_desc'" class="w-4 h-4 text-green-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button class="p-2.5 text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-200" title="مساعدة">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div x-show="filterModalOpen" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <!-- Backdrop -->
        <div x-show="filterModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity"
            @click="filterModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="filterModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl flex flex-col max-h-[80vh]">

                <!-- Header -->
                <div class="bg-white px-[1.05rem] pt-5 pb-4 sm:px-[1.05rem] sm:pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold leading-6 text-gray-900" id="modal-title">تصفية النتائج</h3>
                        <button @click="filterModalOpen = false"
                            class="text-gray-400 hover:text-gray-500 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200">
                        <template x-for="tab in ['books', 'authors', 'sections']">
                            <button @click="activeTab = tab"
                                class="flex-1 pb-3 text-sm font-bold text-center border-b-2 transition-colors duration-200"
                                :class="activeTab === tab ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                <span
                                    x-text="tab === 'books' ? 'الكتب' : (tab === 'authors' ? 'المؤلفين' : 'الأقسام')"></span>
                                <span class="mr-1 text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-full"
                                    x-show="$store.search.selectedFilters[tab].length > 0"
                                    x-text="$store.search.selectedFilters[tab].length"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Search & Content -->
                <div class="flex-1 overflow-hidden flex flex-col bg-gray-50" x-init="initFilter()">
                    <!-- Inner Search -->
                    <div class="px-[1.05rem] py-4 bg-white border-b border-gray-100">
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" x-model="filterSearch"
                                @input="if(activeTab === 'books') searchBooks(); else if(activeTab === 'authors') searchAuthors();"
                                class="block w-full rounded-lg border-gray-300 pr-10 focus:border-green-500 focus:ring-green-500 text-[1rem] leading-[1.35rem] bg-gray-50"
                                :placeholder="'بحث في ' + (activeTab === 'books' ? 'الكتب' : (activeTab === 'authors' ? 'المؤلفين' : 'الأقسام')) + '...'">
                        </div>
                    </div>

                    <!-- List Area -->
                    <div class="flex-1 overflow-y-auto p-2">
                        <ul class="space-y-1">
                            <!-- Books Tab (Dynamic) -->
                            <template x-if="activeTab === 'books'">
                                <div>
                                    <!-- Loading State -->
                                    <template x-if="booksLoading && books.length === 0">
                                        <li class="py-8 text-center text-gray-500 text-sm">
                                            <svg class="animate-spin h-6 w-6 mx-auto text-green-600 mb-2"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            جاري تحميل الكتب...
                                        </li>
                                    </template>

                                    <!-- Books List -->
                                    <template x-for="item in books" :key="item.id">
                                        <li class="relative flex items-start py-2 px-[1.05rem] hover:bg-white hover:shadow-sm rounded-lg transition-all cursor-pointer"
                                            @click="
                                                if ($store.search.selectedFilters.books.includes(item.id)) {
                                                    $store.search.selectedFilters.books = $store.search.selectedFilters.books.filter(id => id !== item.id);
                                                } else {
                                                    $store.search.selectedFilters.books.push(item.id);
                                                }
                                            ">
                                            <div class="min-w-0 flex-1 text-[1rem] leading-[1.35rem]">
                                                <label class="select-none font-medium text-gray-900 cursor-pointer"
                                                    x-text="item.name"></label>
                                            </div>
                                            <div class="mr-3 flex h-6 items-center">
                                                <div class="relative flex items-center justify-center w-5 h-5 border rounded transition-colors"
                                                    :class="$store.search.selectedFilters.books.includes(item.id) ? 'bg-green-600 border-green-600' : 'bg-white border-gray-300'">
                                                    <svg x-show="$store.search.selectedFilters.books.includes(item.id)"
                                                        class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </li>
                                    </template>

                                    <!-- Load More Button -->
                                    <li x-show="hasMoreBooks && !booksLoading && books.length > 0"
                                        class="py-4 text-center sticky bottom-0 bg-gradient-to-t from-white to-transparent">
                                        <button @click="loadMoreBooks()"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-700 font-bold rounded-lg border-2 border-green-200 hover:border-green-400 transition-all duration-200 transform hover:scale-105">
                                            <span>عرض المزيد من الكتب</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                            </svg>
                                        </button>
                                    </li>

                                    <!-- Loading More Indicator -->
                                    <li x-show="booksLoading && books.length > 0"
                                        class="py-3 text-center text-gray-400 text-sm">
                                        <svg class="animate-spin h-4 w-4 mx-auto text-green-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </li>

                                    <!-- Empty State -->
                                    <li x-show="!booksLoading && books.length === 0"
                                        class="py-8 text-center text-gray-500 text-sm">
                                        لا توجد نتائج مطابقة
                                    </li>
                                </div>
                            </template>

                            <!-- Authors Tab (Dynamic with Pagination) -->
                            <template x-if="activeTab === 'authors'">
                                <div>
                                    <!-- Loading State -->
                                    <template x-if="authorsLoading && authors.length === 0">
                                        <li class="py-8 text-center text-gray-500 text-sm">
                                            <svg class="animate-spin h-6 w-6 mx-auto text-green-600 mb-2"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            جاري تحميل المؤلفين...
                                        </li>
                                    </template>

                                    <!-- Authors List -->
                                    <template x-for="item in authors" :key="item.id">
                                        <li class="relative flex items-start py-2 px-[1.05rem] hover:bg-white hover:shadow-sm rounded-lg transition-all cursor-pointer"
                                            @click="
                                                if ($store.search.selectedFilters.authors.includes(item.id)) {
                                                    $store.search.selectedFilters.authors = $store.search.selectedFilters.authors.filter(id => id !== item.id);
                                                } else {
                                                    $store.search.selectedFilters.authors.push(item.id);
                                                }
                                            ">
                                            <div class="min-w-0 flex-1 text-[1rem] leading-[1.35rem]">
                                                <label class="select-none font-medium text-gray-900 cursor-pointer"
                                                    x-text="item.name"></label>
                                            </div>
                                            <div class="mr-3 flex h-6 items-center">
                                                <div class="relative flex items-center justify-center w-5 h-5 border rounded transition-colors"
                                                    :class="$store.search.selectedFilters.authors.includes(item.id) ? 'bg-green-600 border-green-600' : 'bg-white border-gray-300'">
                                                    <svg x-show="$store.search.selectedFilters.authors.includes(item.id)"
                                                        class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </li>
                                    </template>

                                    <!-- Load More Button -->
                                    <li x-show="hasMoreAuthors && !authorsLoading && authors.length > 0"
                                        class="py-4 text-center sticky bottom-0 bg-gradient-to-t from-white to-transparent">
                                        <button @click="loadMoreAuthors()"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-700 font-bold rounded-lg border-2 border-green-200 hover:border-green-400 transition-all duration-200 transform hover:scale-105">
                                            <span>عرض المزيد من المؤلفين</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                            </svg>
                                        </button>
                                    </li>

                                    <!-- Loading More Indicator -->
                                    <li x-show="authorsLoading && authors.length > 0"
                                        class="py-3 text-center text-gray-400 text-sm">
                                        <svg class="animate-spin h-4 w-4 mx-auto text-green-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </li>

                                    <!-- Empty State -->
                                    <li x-show="!authorsLoading && authors.length === 0"
                                        class="py-8 text-center text-gray-500 text-sm">
                                        لا توجد نتائج مطابقة
                                    </li>
                                </div>
                            </template>

                            <!-- Sections Tab (All loaded, filtered locally) -->
                            <template x-if="activeTab === 'sections'">
                                <div>
                                    <!-- Loading State -->
                                    <template x-if="sectionsLoading">
                                        <li class="py-8 text-center text-gray-500 text-sm">
                                            <svg class="animate-spin h-6 w-6 mx-auto text-green-600 mb-2"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            جاري تحميل الأقسام...
                                        </li>
                                    </template>

                                    <!-- Sections List (filtered locally) -->
                                    <template x-for="item in filteredSections" :key="item.id">
                                        <li class="relative flex items-start py-2 px-[1.05rem] hover:bg-white hover:shadow-sm rounded-lg transition-all cursor-pointer"
                                            @click="
                                                if ($store.search.selectedFilters.sections.includes(item.id)) {
                                                    $store.search.selectedFilters.sections = $store.search.selectedFilters.sections.filter(id => id !== item.id);
                                                } else {
                                                    $store.search.selectedFilters.sections.push(item.id);
                                                }
                                            ">
                                            <div class="min-w-0 flex-1 text-[1rem] leading-[1.35rem]">
                                                <label class="select-none font-medium text-gray-900 cursor-pointer"
                                                    x-text="item.name"></label>
                                            </div>
                                            <div class="mr-3 flex h-6 items-center">
                                                <div class="relative flex items-center justify-center w-5 h-5 border rounded transition-colors"
                                                    :class="$store.search.selectedFilters.sections.includes(item.id) ? 'bg-green-600 border-green-600' : 'bg-white border-gray-300'">
                                                    <svg x-show="$store.search.selectedFilters.sections.includes(item.id)"
                                                        class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </li>
                                    </template>

                                    <!-- Empty State -->
                                    <li x-show="!sectionsLoading && filteredSections.length === 0"
                                        class="py-8 text-center text-gray-500 text-sm">
                                        لا توجد نتائج مطابقة
                                    </li>
                                </div>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-white px-[1.05rem] py-3 gap-3 flex flex-row-reverse border-t border-gray-100">
                    <button type="button"
                        class="inline-flex w-full justify-center rounded-lg bg-green-600 px-[1.05rem] py-2 text-[1rem] leading-[1.35rem] font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto transition-colors"
                        @click="filterModalOpen = false; $store.search.performSearch()">
                        تطبيق
                    </button>
                    <button type="button"
                        class="mt-0 inline-flex w-full justify-center rounded-lg bg-white px-[1.05rem] py-2 text-[1rem] leading-[1.35rem] font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto transition-colors"
                        @click="filterModalOpen = false">
                        إلغاء
                    </button>
                    <button type="button" class="mr-auto text-sm text-gray-500 hover:text-red-600 transition-colors"
                        @click="$store.search.selectedFilters = { books: [], authors: [], sections: [] }; $store.search.performSearch()">
                        مسح الكل
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>