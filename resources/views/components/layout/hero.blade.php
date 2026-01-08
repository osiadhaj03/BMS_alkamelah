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

                <!-- Filter Icon (Right side) -->
                <button @click="window.location.href='{{ route('search.static') }}'"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-green-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                </button>

                <!-- Search Icon (Left side) -->
                <button @click="handleSearch()"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 rounded-full hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
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
</div>

<script>
    function heroSearch() {
        return {
            query: '',
            searchMode: 'content',
            showDropdown: false,
            suggestions: [],
            loadingSuggestions: false,

            get placeholderText() {
                if (this.searchMode === 'books') return 'ابحث في عناوين الكتب...';
                if (this.searchMode === 'authors') return 'ابحث في المؤلفين...';
                return 'ابحث في محتوى الكتب...';
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
                        this.suggestions = (data.data || []).slice(0, 6).map(b => ({ id: b.id, name: b.title, type: 'book' }));
                    } else if (this.searchMode === 'authors') {
                        const response = await fetch(`/api/authors?search=${encodeURIComponent(this.query)}`);
                        const data = await response.json();
                        this.suggestions = (data.data || []).slice(0, 6).map(a => ({ id: a.id, name: a.name, type: 'author' }));
                    }
                } catch (e) {
                    console.error('Error fetching suggestions:', e);
                }
                this.loadingSuggestions = false;
            },

            getSuggestionUrl(item) {
                if (item.type === 'book') {
                    return `/book/${item.id}`;
                }
                return `/authors?search=${encodeURIComponent(item.name)}`;
            },

            handleSearch() {
                if (!this.query.trim()) return;

                if (this.searchMode === 'books') {
                    window.location.href = `/books?search=${encodeURIComponent(this.query)}`;
                } else if (this.searchMode === 'authors') {
                    window.location.href = `/authors?search=${encodeURIComponent(this.query)}`;
                } else {
                    window.location.href = `/search?q=${encodeURIComponent(this.query)}`;
                }
            }
        }
    }
</script>