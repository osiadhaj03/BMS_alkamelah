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
    x-data="heroSearch()">
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

        <!-- Search Type Buttons -->
        <div class="flex justify-center gap-4 mb-6">
            <button @click="searchMode = 'authors'" class="px-8 py-3 rounded-full font-medium text-base transition-all"
                :class="searchMode === 'authors' ? 'bg-green-700 text-white' : 'bg-white text-gray-700 border-2 border-gray-400'">
                المؤلفين
            </button>
            <button @click="searchMode = 'content'" class="px-8 py-3 rounded-full font-medium text-base transition-all"
                :class="searchMode === 'content' ? 'bg-green-700 text-white' : 'bg-white text-gray-700 border-2 border-gray-400'">
                محتوى الكتب
            </button>
            <button @click="searchMode = 'books'" class="px-8 py-3 rounded-full font-medium text-base transition-all"
                :class="searchMode === 'books' ? 'bg-green-700 text-white' : 'bg-white text-gray-700 border-2 border-gray-400'">
                عناوين الكتب
            </button>
        </div>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto relative">
            <div class="relative" dir="rtl">
                <input type="text" x-model="query" @input.debounce.300ms="fetchSuggestions()"
                    @focus="showDropdown = true" @click.outside="showDropdown = false" @keydown.enter="handleSearch()"
                    :placeholder="placeholderText"
                    class="w-full px-6 py-4 pr-14 pl-14 text-lg border-2 border-gray-300 rounded-full focus:outline-none focus:border-green-600 text-right bg-white">

                <!-- Search Icon (Right side) -->
                <button @click="handleSearch()"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 rounded-full hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>

                <!-- Filter Icon (Left side) -->
                <button @click="filterModalOpen = true"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-green-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    <span x-show="getActiveFiltersCount() > 0"
                        class="absolute -top-2 -left-2 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold"
                        x-text="getActiveFiltersCount()"></span>
                </button>
            </div>

            <!-- Suggestions Dropdown -->
            <div x-show="showDropdown && suggestions.length > 0 && (searchMode === 'books' || searchMode === 'authors')"
                x-transition
                class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden max-h-80 overflow-y-auto text-right">
                <template x-for="item in suggestions" :key="item.id">
                    <a :href="getSuggestionUrl(item)"
                        class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors">
                        <div class="font-medium text-gray-900" x-text="item.name || item.title"></div>
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
        </div>
    </div>

    <!-- Filter Modal for Books -->
    <div x-show="filterModalOpen && searchMode === 'books'" style="display: none;"
        class="fixed inset-0 z-[100] overflow-y-auto" aria-modal="true">

        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-transition
                class="relative transform overflow-hidden rounded-xl bg-white text-right shadow-xl w-full max-w-lg flex flex-col max-h-[80vh]">

                <!-- Header -->
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

                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200">
                        <button @click="booksFilterTab = 'sections'"
                            class="flex-1 pb-3 text-sm font-bold text-center border-b-2 transition-colors" :class="booksFilterTab === 'sections' ? 'border-green-600 text-green-600' :
                                'border-transparent text-gray-500'">
                            الأقسام
                            <span x-show="sectionFilters.length > 0"
                                class="mr-1 text-xs bg-green-100 text-green-600 px-1.5 py-0.5 rounded-full"
                                x-text="sectionFilters.length"></span>
                        </button>
                        <button @click="booksFilterTab = 'authors'"
                            class="flex-1 pb-3 text-sm font-bold text-center border-b-2 transition-colors" :class="booksFilterTab === 'authors' ? 'border-green-600 text-green-600' :
                                'border-transparent text-gray-500'">
                            المؤلفين
                            <span x-show="authorFilters.length > 0"
                                class="mr-1 text-xs bg-green-100 text-green-600 px-1.5 py-0.5 rounded-full"
                                x-text="authorFilters.length"></span>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50 max-h-72">
                    <!-- Sections Tab -->
                    <div x-show="booksFilterTab === 'sections'">
                        <div class="mb-3">
                            <input type="text" x-model="sectionSearch" @input.debounce.300ms="fetchSections()"
                                placeholder="بحث في الأقسام..." class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <ul class="space-y-1">
                            <template x-for="section in sections" :key="section.id">
                                <li class="flex items-center py-2 px-4 hover:bg-white rounded-lg cursor-pointer"
                                    @click="toggleFilter('section', section.id)">
                                    <div class="flex-1 font-medium" x-text="section.name" style="font-size: 1rem;">
                                    </div>
                                    <div class="w-5 h-5 border rounded flex items-center justify-center" :class="sectionFilters.includes(section.id) ?
                                            'bg-green-600 border-green-600' : 'border-gray-300'">
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

                    <!-- Authors Tab -->
                    <div x-show="booksFilterTab === 'authors'">
                        <div class="mb-3">
                            <input type="text" x-model="authorSearch" @input.debounce.300ms="fetchAuthorsForFilter()"
                                placeholder="بحث في المؤلفين..." class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <ul class="space-y-1">
                            <template x-for="author in authorsForFilter" :key="author.id">
                                <li class="flex items-center py-2 px-4 hover:bg-white rounded-lg cursor-pointer"
                                    @click="toggleFilter('author', author.id)">
                                    <div class="flex-1 font-medium" x-text="author.name" style="font-size: 1rem;">
                                    </div>
                                    <div class="w-5 h-5 border rounded flex items-center justify-center" :class="authorFilters.includes(author.id) ? 'bg-green-600 border-green-600' :
                                            'border-gray-300'">
                                        <svg x-show="authorFilters.includes(author.id)" class="w-3.5 h-3.5 text-white"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-white px-6 py-3 gap-3 flex flex-row-reverse border-t border-gray-100">
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-500">تطبيق</button>
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-white text-gray-900 rounded-lg font-semibold ring-1 ring-gray-300 hover:bg-gray-50">إلغاء</button>
                    <button @click="clearBooksFilters()" class="mr-auto text-sm text-gray-500 hover:text-red-600">مسح
                        الكل</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal for Authors -->
    <div x-show="filterModalOpen && searchMode === 'authors'" style="display: none;"
        class="fixed inset-0 z-[100] overflow-y-auto" aria-modal="true">

        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-transition
                class="relative transform overflow-hidden rounded-xl bg-white text-right shadow-xl w-full max-w-lg flex flex-col max-h-[80vh]">

                <!-- Header -->
                <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">تصفية المؤلفين</h3>
                        <button @click="filterModalOpen = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200">
                        <button @click="authorsFilterTab = 'madhhab'"
                            class="flex-1 pb-3 text-sm font-bold text-center border-b-2 transition-colors" :class="authorsFilterTab === 'madhhab' ? 'border-green-600 text-green-600' :
                                'border-transparent text-gray-500'">
                            المذهب
                        </button>
                        <button @click="authorsFilterTab = 'century'"
                            class="flex-1 pb-3 text-sm font-bold text-center border-b-2 transition-colors" :class="authorsFilterTab === 'century' ? 'border-green-600 text-green-600' :
                                'border-transparent text-gray-500'">
                            القرن
                        </button>
                        <button @click="authorsFilterTab = 'daterange'"
                            class="flex-1 pb-3 text-sm font-bold text-center border-b-2 transition-colors" :class="authorsFilterTab === 'daterange' ? 'border-green-600 text-green-600' :
                                'border-transparent text-gray-500'">
                            نطاق التاريخ
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50 max-h-72">
                    <!-- Madhhab Tab -->
                    <div x-show="authorsFilterTab === 'madhhab'">
                        <ul class="space-y-2">
                            <template x-for="m in availableMadhhabs" :key="m">
                                <li class="flex items-center py-3 px-4 hover:bg-white rounded-lg cursor-pointer"
                                    @click="toggleMadhhabFilter(m)">
                                    <div class="flex-1 font-medium" x-text="m"
                                        style="font-size: 1rem; line-height: 1.5rem;"></div>
                                    <div class="w-5 h-5 border rounded flex items-center justify-center" :class="madhhabFilters.includes(m) ? 'bg-green-600 border-green-600' :
                                            'border-gray-300'">
                                        <svg x-show="madhhabFilters.includes(m)" class="w-3.5 h-3.5 text-white"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Century Tab -->
                    <div x-show="authorsFilterTab === 'century'">
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="(name, num) in availableCenturies" :key="num">
                                <button @click="toggleCenturyFilter(parseInt(num))"
                                    class="py-4 px-2 text-center rounded-lg border-2 transition-all font-medium" :class="centuryFilters.includes(parseInt(num)) ?
                                        'bg-green-600 border-green-600 text-white' :
                                        'bg-white border-gray-200 text-gray-700'" x-text="name"
                                    style="font-size: 1rem; line-height: 1.5rem;">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Date Range Tab -->
                    <div x-show="authorsFilterTab === 'daterange'">
                        <div class="bg-white rounded-lg p-5 shadow-sm">
                            <p class="text-gray-600 mb-5" style="font-size: 1rem; line-height: 1.5rem;">أدخل نطاق سنة
                                الوفاة بالتقويم الهجري:</p>
                            <div class="flex gap-4 items-center">
                                <div class="flex-1">
                                    <label class="block font-medium text-gray-700 mb-2" style="font-size: 1rem;">من
                                        سنة</label>
                                    <input type="number" x-model="deathDateFrom" placeholder="مثال: 150" min="1"
                                        max="1500" class="w-full px-4 py-3 rounded-lg border border-gray-300"
                                        style="font-size: 1rem;">
                                </div>
                                <span class="text-gray-400 pt-8 text-xl">—</span>
                                <div class="flex-1">
                                    <label class="block font-medium text-gray-700 mb-2" style="font-size: 1rem;">إلى
                                        سنة</label>
                                    <input type="number" x-model="deathDateTo" placeholder="مثال: 200" min="1"
                                        max="1500" class="w-full px-4 py-3 rounded-lg border border-gray-300"
                                        style="font-size: 1rem;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-white px-6 py-3 gap-3 flex flex-row-reverse border-t border-gray-100">
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-500">تطبيق</button>
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-white text-gray-900 rounded-lg font-semibold ring-1 ring-gray-300 hover:bg-gray-50">إلغاء</button>
                    <button @click="clearAuthorsFilters()" class="mr-auto text-sm text-gray-500 hover:text-red-600">مسح
                        الكل</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal for Content -->
    <div x-show="filterModalOpen && searchMode === 'content'" style="display: none;"
        class="fixed inset-0 z-[100] overflow-y-auto" aria-modal="true">

        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" @click="filterModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div x-transition
                class="relative transform overflow-hidden rounded-xl bg-white text-right shadow-xl w-full max-w-lg flex flex-col max-h-[80vh]">

                <!-- Header -->
                <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">إعدادات البحث في المحتوى</h3>
                        <button @click="filterModalOpen = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                    <!-- Search Type -->
                    <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
                        <h4 class="font-bold text-gray-800 text-sm mb-3">نوع البحث</h4>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="heroSearchType" value="exact_match" x-model="searchType"
                                    class="h-4 w-4" style="color: #2C6E4A;">
                                <span class="text-sm font-medium">البحث المطابق</span>
                            </label>
                            <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="heroSearchType" value="flexible_match" x-model="searchType"
                                    class="h-4 w-4" style="color: #2C6E4A;">
                                <span class="text-sm font-medium">البحث الغير مطابق</span>
                            </label>
                        </div>
                    </div>

                    <!-- Word Order -->
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <h4 class="font-bold text-gray-800 text-sm mb-3">ترتيب الكلمات</h4>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="heroWordOrder" value="consecutive" x-model="wordOrder"
                                    class="h-4 w-4" style="color: #2C6E4A;">
                                <span class="text-sm font-medium">كلمات متتالية</span>
                            </label>
                            <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="heroWordOrder" value="any_order" x-model="wordOrder"
                                    class="h-4 w-4" style="color: #2C6E4A;">
                                <span class="text-sm font-medium">أي ترتيب</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-white px-6 py-3 gap-3 flex flex-row-reverse border-t border-gray-100">
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-500">تطبيق</button>
                    <button @click="filterModalOpen = false"
                        class="px-4 py-2 bg-white text-gray-900 rounded-lg font-semibold ring-1 ring-gray-300 hover:bg-gray-50">إلغاء</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function heroSearch() {
        return {
            query: '',
            searchMode: 'content',
            showDropdown: false,
            filterModalOpen: false,
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

            // Authors filters
            authorsFilterTab: 'madhhab',
            madhhabFilters: [],
            centuryFilters: [],
            deathDateFrom: '',
            deathDateTo: '',

            // Content filters
            searchType: 'flexible_match',
            wordOrder: 'any_order',

            availableMadhhabs: ['المذهب الحنفي', 'المذهب المالكي', 'المذهب الشافعي', 'المذهب الحنبلي'],
            availableCenturies: {
                1: 'الأول',
                2: 'الثاني',
                3: 'الثالث',
                4: 'الرابع',
                5: 'الخامس',
                6: 'السادس',
                7: 'السابع',
                8: 'الثامن',
                9: 'التاسع',
                10: 'العاشر',
                11: 'الحادي عشر',
                12: 'الثاني عشر',
                13: 'الثالث عشر',
                14: 'الرابع عشر',
                15: 'الخامس عشر'
            },

            get placeholderText() {
                if (this.searchMode === 'books') return 'ابحث في عناوين الكتب...';
                if (this.searchMode === 'authors') return 'ابحث في المؤلفين...';
                return 'ابحث في محتوى الكتب...';
            },

            init() {
                this.fetchSections();
                this.fetchAuthorsForFilter();

                // Read from URL to sync filters
                const params = new URLSearchParams(window.location.search);
                const path = window.location.pathname;

                if (path.includes('/authors')) {
                    this.searchMode = 'authors';
                    if (params.has('search')) this.query = params.get('search');

                    // Parse arrays from URL
                    const mFilters = params.getAll('madhhabFilters[]');
                    if (mFilters.length > 0) this.madhhabFilters = mFilters;

                    const cFilters = params.getAll('centuryFilters[]');
                    if (cFilters.length > 0) this.centuryFilters = cFilters.map(c => parseInt(c));

                    if (params.has('deathDateFrom')) this.deathDateFrom = params.get('deathDateFrom');
                    if (params.has('deathDateTo')) this.deathDateTo = params.get('deathDateTo');
                } else if (path.includes('/books')) {
                    this.searchMode = 'books';
                    if (params.has('search')) this.query = params.get('search');

                    const sFilters = params.getAll('sectionFilters[]');
                    if (sFilters.length > 0) this.sectionFilters = sFilters.map(s => parseInt(s));

                    const aFilters = params.getAll('authorFilters[]');
                    if (aFilters.length > 0) this.authorFilters = aFilters.map(a => parseInt(a));
                } else if (path.includes('/search')) {
                    this.searchMode = 'content';
                    if (params.has('q')) this.query = params.get('q');
                    if (params.has('search_type')) this.searchType = params.get('search_type');
                    if (params.has('word_order')) this.wordOrder = params.get('word_order');
                }
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
                        this.suggestions = (data.data || []).slice(0, 6).map(b => ({
                            id: b.id,
                            name: b.title,
                            type: 'book'
                        }));
                    } else if (this.searchMode === 'authors') {
                        const response = await fetch(`/api/authors?search=${encodeURIComponent(this.query)}`);
                        const data = await response.json();
                        this.suggestions = (data.data || []).slice(0, 6).map(a => ({
                            id: a.id,
                            name: a.name,
                            type: 'author'
                        }));
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
                } catch (e) {
                    console.error(e);
                }
            },

            async fetchAuthorsForFilter() {
                try {
                    const response = await fetch(`/api/authors?search=${encodeURIComponent(this.authorSearch)}`);
                    const data = await response.json();
                    this.authorsForFilter = data.data || [];
                } catch (e) {
                    console.error(e);
                }
            },

            getSuggestionUrl(item) {
                if (item.type === 'book') {
                    return `/book/${item.id}`;
                }
                return `/author/${item.id}`;
            },

            handleSearch() {
                const query = this.query ? this.query.trim() : '';

                let url = '';
                const params = new URLSearchParams();
                if (query) params.set('search', query);

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
                    if (!query) return; // Search content requires query
                    url = '/search';
                    params.set('q', query);
                    params.delete('search'); // Use 'q' instead of 'search' for content
                    params.set('search_type', this.searchType);
                    params.set('word_order', this.wordOrder);
                }

                window.location.href = url + '?' + params.toString();
            },

            toggleFilter(type, id) {
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
                    this.madhhabFilters = [...new Set([...this.madhhabFilters, m])];
                }
            },

            toggleCenturyFilter(c) {
                const centuryId = parseInt(c);
                if (this.centuryFilters.includes(centuryId)) {
                    this.centuryFilters = this.centuryFilters.filter(i => i !== centuryId);
                } else {
                    this.centuryFilters = [...new Set([...this.centuryFilters, centuryId])];
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
                } else if (this.searchMode === 'content') {
                    let count = 0;
                    if (this.searchType !== 'flexible_match') count++;
                    if (this.wordOrder !== 'any_order') count++;
                    return count;
                }
                return 0;
            }
        }
    }
</script>