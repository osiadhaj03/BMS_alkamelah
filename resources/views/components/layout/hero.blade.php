@php
    // الحصول على الإحصائيات بشكل ديناميكي
    $booksCount = \App\Models\Book::count();
    $authorsCount = \App\Models\Author::count();
    $pagesCount = \App\Models\Page::count();
    $sectionsCount = \App\Models\BookSection::where('is_active', true)->whereNull('parent_id')->count();
@endphp

<!-- Hero Section with Search -->
<div class="relative py-16 min-h-screen flex items-center" dir="rtl"
    style="background-image: url('{{ asset('images/الأقصى.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;"
    x-data="staticSearch()" @keydown.enter="handleSearch()">
    <!-- Dark overlay for better text readability -->
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center w-full">
        <!-- Main Title -->
        <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
            مكتبة تكاملت موضوعاتها و كتبها
        </h1>

        <!-- Description Paragraph -->
        <p class="text-xl md:text-2xl text-white mb-4 leading-relaxed font-medium max-w-4xl mx-auto">
            اكتشف <span class="text-white font-bold">{{ number_format($booksCount) }}</span> كتاباً في الحديث، الفقه،
            الأدب، البلاغة، والتاريخ والأنساب وغيرها الكثير
        </p>

        <p class="text-lg md:text-xl text-white mb-8 leading-relaxed max-w-4xl mx-auto">
            بأقلام <span class="text-white font-bold">{{ number_format($authorsCount) }}</span> مؤلف عبر <span
                class="text-white font-bold">{{ number_format($pagesCount) }}</span> صفحة موزعة على <span
                class="text-white font-bold">{{ $sectionsCount }}</span> قسم متخصص - كل ذلك متاح لك في مكان واحد
        </p>

        <!-- Search Bar from static-search.blade.php -->
        <div class="max-w-2xl mx-auto relative">
            <div
                class="relative flex items-center shadow-md transition-all duration-200 border border-gray-200 rounded-full bg-white overflow-visible h-12 md:h-14 z-30 focus-within:border-[#2C6E4A] focus-within:ring-1 focus-within:ring-[#2C6E4A]">

                <!-- Search Icon (Right) -->
                <div class="pl-3 pr-4" style="color: #BA4749;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Input -->
                <input type="text" x-model="query" @input.debounce.300ms="fetchSuggestions()"
                    @focus="showDropdown = true" @click.outside="showDropdown = false"
                    class="w-full h-full border-none focus:ring-0 text-lg text-gray-700 placeholder-gray-400 px-0 bg-transparent rounded-full text-right"
                    :placeholder="placeholderText" dir="rtl">

                <!-- Actions (Left) -->
                <div class="flex items-center pl-2 gap-1 h-full">

                    <!-- Filter Button -->
                    <button @click="filterModalOpen = true"
                        class="p-2 mr-1 rounded-full hover:bg-gray-100 transition-colors" style="color: #2C6E4A;"
                        title="تصفية النتائج">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        <span x-show="getActiveFiltersCount() > 0"
                            class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
                            x-text="getActiveFiltersCount()"></span>
                    </button>

                    <!-- Settings Button (Visible only for Content Search) -->
                    <div class="relative h-full flex items-center" x-show="searchMode === 'content'" x-cloak
                        style="display: none;">
                        <button @click="settingsOpen = !settingsOpen" @click.outside="settingsOpen = false"
                            class="p-2 ml-2 rounded-full hover:bg-gray-100 transition-colors"
                            :class="{'bg-gray-100': settingsOpen}" style="color: #2C6E4A;" title="إعدادات البحث">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>

                        <!-- Settings Dropdown -->
                        <div x-show="settingsOpen" x-transition x-cloak style="display: none;"
                            class="absolute top-full left-0 mt-4 w-[300px] sm:w-[400px] bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden text-right">
                            <div class="p-4 grid grid-cols-1 gap-4 text-right">
                                <!-- Search Type -->
                                <div class="space-y-2">
                                    <h4
                                        class="font-bold text-gray-700 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">
                                        نوع البحث</h4>
                                    <div class="flex flex-col gap-1">
                                        <label
                                            class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="searchType" value="exact" x-model="searchType"
                                                class="h-4 w-4" style="color: #2C6E4A;">
                                            <span class="text-sm font-medium">البحث المطابق</span>
                                        </label>
                                        <label
                                            class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="searchType" value="flexible" x-model="searchType"
                                                class="h-4 w-4" style="color: #2C6E4A;">
                                            <span class="text-sm font-medium">البحث الغير مطابق</span>
                                        </label>
                                    </div>
                                </div>
                                <!-- Word Order -->
                                <div class="space-y-2">
                                    <h4
                                        class="font-bold text-gray-700 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">
                                        ترتيب الكلمات</h4>
                                    <div class="flex flex-col gap-1">
                                        <label
                                            class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="wordOrder" value="consecutive" x-model="wordOrder"
                                                class="h-4 w-4" style="color: #2C6E4A;">
                                            <span class="text-sm font-medium">كلمات متتالية</span>
                                        </label>
                                        <label
                                            class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="wordOrder" value="any" x-model="wordOrder"
                                                class="h-4 w-4" style="color: #2C6E4A;">
                                            <span class="text-sm font-medium">أي ترتيب</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggestions Dropdown -->
            <div x-show="showDropdown && suggestions.length > 0 && (searchMode === 'books' || searchMode === 'authors')"
                x-transition x-cloak style="display: none;"
                class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden max-h-80 overflow-y-auto">
                <template x-for="item in suggestions" :key="item.id">
                    <a :href="getSuggestionUrl(item)"
                        class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors text-right">
                        <div class="font-medium text-gray-900" x-text="item.name || item.title"></div>
                        <div x-show="item.extra" class="text-sm text-gray-500" x-text="item.extra"></div>
                    </a>
                </template>
                <div x-show="loadingSuggestions" class="px-4 py-3 text-center text-gray-500">
                    <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Quick Action Buttons (Mode Toggles) -->
            <div class="mt-8 flex justify-center gap-3">
                <button @click="searchMode = 'books'"
                    class="px-6 py-2.5 font-bold rounded-md transition-all shadow-sm hover:shadow-md border border-[#2C6E4A]"
                    :class="searchMode === 'books' ? 'bg-[#2C6E4A] text-white' : 'bg-white text-[#2C6E4A]'"
                    style="font-size: 1rem; line-height: 1.35rem;">
                    بحث في الكتب
                </button>
                <button @click="searchMode = 'authors'"
                    class="px-6 py-2.5 font-bold rounded-md transition-all shadow-sm hover:shadow-md border border-[#2C6E4A]"
                    :class="searchMode === 'authors' ? 'bg-[#2C6E4A] text-white' : 'bg-white text-[#2C6E4A]'"
                    style="font-size: 1rem; line-height: 1.35rem;">
                    بحث في المؤلفين
                </button>
                <button @click="searchMode = 'content'"
                    class="px-6 py-2.5 font-bold rounded-md transition-all shadow-sm hover:shadow-md border border-[#2C6E4A]"
                    :class="searchMode === 'content' ? 'bg-[#2C6E4A] text-white' : 'bg-white text-[#2C6E4A]'"
                    style="font-size: 1rem; line-height: 1.35rem;">
                    بحث في المحتوى
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Modal for Books --}}
    {{-- COMMENTED OUT - TODO: Uncomment when ready to use
    <div x-show="filterModalOpen && searchMode === 'books'" style="display: none;"
        class="fixed inset-0 z-[9999] overflow-y-auto" aria-modal="true" x-cloak>

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-transition
                class="relative transform overflow-hidden rounded-xl bg-white text-right shadow-xl w-full max-w-lg flex flex-col max-h-[80vh]">

                <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">تصفية الكتب</h3>
                        <button @click="filterModalOpen = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex border-b border-gray-200">
                        <button @click="booksFilterTab = 'sections'"
                            class="flex-1 pb-3 text-sm font-bold text-center border-b-2 transition-colors"
                            :class="booksFilterTab === 'sections' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500'">
                            الأقسام
                            <span x-show="sectionFilters.length > 0"
                                class="mr-1 text-xs bg-green-100 text-green-600 px-1.5 py-0.5 rounded-full"
                                x-text="sectionFilters.length"></span>
                        </button>
                        <button @click="booksFilterTab = 'authors'"
                            class="flex-1 pb-3 text-sm font-bold text-center border-b-2 transition-colors"
                            :class="booksFilterTab === 'authors' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-500'">
                            المؤلفين
                            <span x-show="authorFilters.length > 0"
                                class="mr-1 text-xs bg-green-100 text-green-600 px-1.5 py-0.5 rounded-full"
                                x-text="authorFilters.length"></span>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 bg-gray-50 max-h-72">
                    <div x-show="booksFilterTab === 'sections'">
                        <div class="mb-3">
                            <input type="text" x-model="sectionSearch" @input.debounce.300ms="fetchSections()"
                                placeholder="بحث في الأقسام..."
                                class="w-full rounded-lg border-gray-300 text-sm focus:ring-green-500 focus:border-green-500 text-right"
                                dir="rtl">
                        </div>
                        <ul class="space-y-1">
                            <template x-for="section in sections" :key="section.id">
                                <li class="flex items-center py-2 px-4 hover:bg-white rounded-lg cursor-pointer"
                                    @click="toggleFilter('section', section.id)">
                                    <div class="flex-1 font-medium text-right" x-text="section.name"
                                        style="font-size: 1rem;">
                                    </div>
                                    <div class="w-5 h-5 border rounded flex items-center justify-center mr-3"
                                        :class="sectionFilters.includes(section.id) ? 'bg-green-600 border-green-600' : 'border-gray-300'">
                                        <svg x-show="sectionFilters.includes(section.id)" class="w-3.5 h-3.5 text-white"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>


                    <div x-show="booksFilterTab === 'authors'">
                        <div class="mb-3">
                            <input type="text" x-model="authorSearch" @input.debounce.300ms="fetchAuthorsForFilter()"
                                placeholder="بحث في المؤلفين..."
                                class="w-full rounded-lg border-gray-300 text-sm focus:ring-green-500 focus:border-green-500 text-right"
                                dir="rtl">
                        </div>
                        <ul class="space-y-1">
                            <template x-for="author in authorsForFilter" :key="author.id">
                                <li class="flex items-center py-2 px-4 hover:bg-white rounded-lg cursor-pointer"
                                    @click="toggleFilter('author', author.id)">
                                    <div class="flex-1 font-medium text-right" x-text="author.name"
                                        style="font-size: 1rem;"></div>
                                    <div class="w-5 h-5 border rounded flex items-center justify-center mr-3"
                                        :class="authorFilters.includes(author.id) ? 'bg-green-600 border-green-600' : 'border-gray-300'">
                                        <svg x-show="authorFilters.includes(author.id)" class="w-3.5 h-3.5 text-white"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </li>
                            </template>
                        </ul>
                        <div x-show="hasMoreAuthors && authorsForFilter.length > 0" class="mt-4 text-center">
                            <button @click="loadMoreAuthors()"
                                class="text-sm text-green-600 font-semibold hover:text-green-800 transition-colors">
                                عرض المزيد
                            </button>
                        </div>
                    </div>
                </div>


                <div class="bg-white px-6 py-3 gap-3 flex flex-row-reverse border-t border-gray-100">
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-500 transition-colors">تطبيق</button>
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-white text-gray-900 rounded-lg font-semibold ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">إلغاء</button>
                    <button @click="clearBooksFilters()"
                        class="mr-auto text-sm text-gray-500 hover:text-red-600 transition-colors">مسح
                        الكل</button>
                </div>
            </div>
        </div>
    </div>
    --}}


    {{-- Filter Modal for Authors - INCOMPLETE, TODO: Add header and content sections --}}
    {{--
    <div x-show="filterModalOpen && searchMode === 'authors'" style="display: none;"
        class="fixed inset-0 z-[9999] overflow-y-auto" aria-modal="true" x-cloak>

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-transition
                class="relative transform overflow-hidden rounded-xl bg-white text-right shadow-xl w-full max-w-lg flex flex-col max-h-[80vh]">


                <!-- Footer -->
                <div class="bg-white px-6 py-3 gap-3 flex flex-row-reverse border-t border-gray-100">
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-500 transition-colors">تطبيق</button>
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-white text-gray-900 rounded-lg font-semibold ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">إلغاء</button>
                    <button @click="clearAuthorsFilters()"
                        class="mr-auto text-sm text-gray-500 hover:text-red-600 transition-colors">مسح
                        الكل</button>
                </div>
            </div>
        </div>
   </div>
    --}}
</div>

<script>
    function staticSearch() {
        return {
            query: '',
            searchMode: 'books',
            settingsOpen: false,
            filterModalOpen: false,
            showDropdown: false,
            searchType: 'flexible',
            wordOrder: 'any',

            // Suggestions
            suggestions: [],
            loadingSuggestions: false,

            // Books filters
            booksFilterTab: 'sections',
            sectionFilters: [],
            authorFilters: [],
            sections: [],
            authorsForFilter: [],
            sectionSearch: '',
            authorSearch: '',
            authorsPage: 1,
            hasMoreAuthors: true,

            // Authors filters
            authorsFilterTab: 'madhhab',
            madhhabFilters: [],
            centuryFilters: [],
            deathDateFrom: '',
            deathDateTo: '',

            availableMadhhabs: ['المذهب الحنفي', 'المذهب المالكي', 'المذهب الشافعي', 'المذهب الحنبلي'],
            availableCenturies: {
                1: 'القرن الأول',
                2: 'القرن الثاني',
                3: 'القرن الثالث',
                4: 'القرن الرابع',
                5: 'القرن الخامس',
                6: 'القرن السادس',
                7: 'القرن السابع',
                8: 'القرن الثامن',
                9: 'القرن التاسع',
                10: 'القرن العاشر',
                11: 'القرن الحادي عشر',
                12: 'القرن الثاني عشر',
                13: 'القرن الثالث عشر',
                14: 'القرن الرابع عشر',
                15: 'القرن الخامس عشر'
            },

            get placeholderText() {
                if (this.searchMode === 'books') return 'بحث في {{ number_format($booksCount) }} كتاب...';
                if (this.searchMode === 'authors') return 'بحث في {{ number_format($authorsCount) }} مؤلف...';
                return 'بحث في {{ number_format($booksCount) }} كتاب و {{ number_format($pagesCount) }} صفحة...';
            },

            init() {
                this.fetchSections();
                this.fetchAuthorsForFilter();
            },

            async fetchSuggestions() {
                if (!this.query.trim() || this.query.length < 2) {
                    this.suggestions = [];
                    return;
                }

                this.loadingSuggestions = true;
                try {
                    if (this.searchMode === 'books') {
                        const response = await fetch(`/api/books?search=${encodeURIComponent(this.query)}`);
                        const data = await response.json();
                        this.suggestions = (data.data || []).slice(0, 8).map(b => ({ id: b.id, name: b.title, type: 'book' }));
                    } else if (this.searchMode === 'authors') {
                        const response = await fetch(`/api/authors?search=${encodeURIComponent(this.query)}`);
                        const data = await response.json();
                        this.suggestions = (data.data || []).slice(0, 8).map(a => ({ id: a.id, name: a.name, type: 'author' }));
                    }
                } catch (e) {
                    console.error('Error fetching suggestions:', e);
                }
                this.loadingSuggestions = false;
            },

            async fetchSections() {
                try {
                    const response = await fetch(`/api/sections?search=${encodeURIComponent(this.sectionSearch)}`);
                    this.sections = await response.json();
                } catch (e) { console.error(e); }
            },

            async fetchAuthorsForFilter(loadMore = false) {
                if (!loadMore) {
                    this.authorsPage = 1;
                    this.hasMoreAuthors = true;
                }

                try {
                    const response = await fetch(`/api/authors?search=${encodeURIComponent(this.authorSearch)}&page=${this.authorsPage}`);
                    const data = await response.json();

                    const newAuthors = data.data || [];

                    if (loadMore) {
                        this.authorsForFilter = [...this.authorsForFilter, ...newAuthors];
                    } else {
                        this.authorsForFilter = newAuthors;
                    }

                    // Check if we reached the last page or no more results
                    if (data.current_page >= data.last_page || newAuthors.length === 0) {
                        this.hasMoreAuthors = false;
                    }

                } catch (e) { console.error(e); }
            },

            loadMoreAuthors() {
                if (!this.hasMoreAuthors) return;
                this.authorsPage++;
                this.fetchAuthorsForFilter(true);
            },

            getSuggestionUrl(item) {
                if (item.type === 'book') {
                    return `/book/${item.id}`;
                }
                return `/authors?search=${encodeURIComponent(item.name)}`;
            },

            handleSearch() {
                if (!this.query.trim()) return;

                let url = '';
                const params = new URLSearchParams();
                params.set('search', this.query);

                if (this.searchMode === 'books') {
                    url = '/books';
                    this.sectionFilters.forEach(id => params.append('sectionFilters[]', id));
                    this.authorFilters.forEach(id => params.append('authorFilters[]', id));
                } else if (this.searchMode === 'authors') {
                    url = '/authors';
                    this.madhhabFilters.forEach(m => params.append('madhhabFilters[]', m));
                    this.centuryFilters.forEach(c => params.append('centuryFilters[]', c));
                    if (this.deathDateFrom) params.set('deathDateFrom', this.deathDateFrom);
                    if (this.deathDateTo) params.set('deathDateTo', this.deathDateTo);
                } else {
                    url = '/search';
                    params.set('q', this.query);
                    params.set('search_type', this.searchType);
                    params.set('word_order', this.wordOrder);
                }

                window.location.href = url + '?' + params.toString();
            },

            toggleFilter(type, id) {
                // Ensure ID is treated consistently (as int) if possible, or just exact match
                // We'll trust the API sends numbers, but just in case, we don't force cast unless needed.
                // However, standard equality check in JS includes type, so let's be safe.

                if (type === 'section') {
                    if (this.sectionFilters.includes(id)) {
                        this.sectionFilters = this.sectionFilters.filter(i => i !== id);
                    } else {
                        this.sectionFilters.push(id);
                    }
                } else if (type === 'author') {
                    if (this.authorFilters.includes(id)) {
                        this.authorFilters = this.authorFilters.filter(i => i !== id);
                    } else {
                        this.authorFilters.push(id);
                    }
                }
            },

            toggleMadhhabFilter(m) {
                if (this.madhhabFilters.includes(m)) {
                    this.madhhabFilters = this.madhhabFilters.filter(i => i !== m);
                } else {
                    this.madhhabFilters.push(m);
                }
            },

            toggleCenturyFilter(c) {
                if (this.centuryFilters.includes(c)) {
                    this.centuryFilters = this.centuryFilters.filter(i => i !== c);
                } else {
                    this.centuryFilters.push(c);
                }
            },

            clearBooksFilters() {
                this.sectionFilters = [];
                this.authorFilters = [];
            },

            clearAuthorsFilters() {
                this.madhhabFilters = [];
                this.centuryFilters = [];
                this.deathDateFrom = '';
                this.deathDateTo = '';
            },

            getActiveFiltersCount() {
                if (this.searchMode === 'books') {
                    return this.sectionFilters.length + this.authorFilters.length;
                } else if (this.searchMode === 'authors') {
                    let count = this.madhhabFilters.length + this.centuryFilters.length;
                    if (this.deathDateFrom || this.deathDateTo) count++;
                    return count;
                }
                return 0;
            }
        }
    }
</script>