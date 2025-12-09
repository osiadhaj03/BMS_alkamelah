<!-- Search Section - Hero Section with Full Screen -->
<div id="search-section" class="relative min-h-screen flex items-center justify-center" 
     style="background-image: url('{{ asset('images/search-bg.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
    
    <!-- Dark Overlay for better text readability -->
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    
    <!-- Content Container -->
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20" style="padding-top: 120px;">
        <div class="text-center">
            
            <!-- Main Title -->
            <div class="mb-8">
                <h1 class="text-5xl md:text-6xl lg:text-7xl text-white font-bold mb-6 leading-tight">
                    مكتبة تكاملت موضوعاتها و كتبها
                </h1>
                
                <!-- Smart Description with Dynamic Stats -->
                <div class="max-w-4xl mx-auto">
                    <p class="text-xl md:text-2xl text-white/90 mb-4 leading-relaxed">
                        اكتشف 
                        <span class="text-400 font-bold ">{{ number_format($stats['total_books']) }}</span> <!-- text-amber-400 font-bold bg-amber-400/20 px-2 py-1 rounded-lg shadow-lg-->
                        كتاباً في الحديث، الفقه، الأدب، البلاغة، والتاريخ والأنساب وغيرها الكثير
                    </p>
                    <p class="text-lg md:text-xl text-white/80 mb-8">
                        بأقلام 
                        <span class="text-400 font-bold ">{{ number_format($stats['total_authors']) }}</span> 
                        <!-- text-emerald-400 font-bold bg-emerald-400/20 px-2 py-1 rounded-lg shadow-lg-->
                        مؤلف عبر 
                        <span class="text-400 font-bold ">{{ number_format($stats['total_pages']) }}</span> 
                        <!-- text-cyan-400 font-bold bg-cyan-400/20 px-2 py-1 rounded-lg shadow-lg-->
                        صفحة موزعة على 
                        <span class="text-400 font-bold ">{{ number_format($stats['total_sections']) }}</span> 
                        <!-- text-violet-400 font-bold bg-violet-400/20 px-2 py-1 rounded-lg shadow-lg-->
                        قسم متخصص - كل ذلك متاح لك في مكان واحد
                    </p>
                </div>
            </div>

            <!-- Search Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 mb-10 justify-center">
                <button id="search-authors-btn" 
                        class="search-type-btn bg-white/20 backdrop-blur-sm text-white border-2 border-white/50 transition-all duration-300 hover:bg-white hover:text-green-800 hover:shadow-lg transform hover:-translate-y-1 px-8 py-3 rounded-3xl font-bold shadow-md text-center">
                    المؤلفين
                </button>
                <button id="search-books-btn" 
                        class="search-type-btn bg-white text-green-800 border-2 border-white transition-all duration-300 hover:bg-white/90 hover:shadow-lg transform hover:-translate-y-1 px-8 py-3 rounded-3xl font-bold shadow-md text-center relative active">
                    <span class="absolute inset-0 border-2 border-green-600 rounded-3xl"></span>
                    عناوين الكتب
                </button>

            </div>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto relative z-[100]">
                <div class="relative bg-white/95 backdrop-blur-sm rounded-full px-16 py-5 flex items-center gap-3 shadow-2xl border border-white/20 hover:shadow-3xl transition-all duration-300">
                    <img src="{{ asset('images/iconly-light-search0.svg') }}" alt="Search" class="w-6 h-6 text-gray-400">
                    
                    <input
                        type="text"
                        id="search-input"
                        placeholder="إبحث في عناوين الكتب ..."
                        autocomplete="off"
                        class="flex-1 bg-transparent border-none focus:ring-0 text-gray-700 placeholder-gray-500 text-lg focus:outline-none">
                    
                    <!-- Filter Icon Button for Books -->
                    <div id="section-filter-container" class="flex items-center">
                        <button id="section-filter-btn" 
                                class="p-2 bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors duration-200"
                                type="button">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <button type="button"
                            id="search-btn"
                            class="absolute left-4 p-3 rounded-full bg-green-600 text-white transition-all duration-300 hover:bg-green-700 hover:scale-110 active:scale-95 shadow-lg">
                        <img src="{{ asset('images/iconly-bold-send0.svg') }}" alt="Search icon" class="w-5 h-5 filter brightness-0 invert">
                    </button>
                </div>

                <!-- Dropdown Results -->
                <div id="search-dropdown" 
                     class="absolute top-full left-0 right-0 bg-white/95 backdrop-blur-sm border border-gray-200 rounded-2xl shadow-2xl mt-2 max-h-80 md:max-h-96 overflow-y-auto z-[99999] hidden"
                     style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
                    <div id="search-results" class="p-2 md:p-4">
                        <!-- Results will be populated here -->
                    </div>
                    <div id="search-loading" class="p-6 md:p-8 text-center text-gray-500 hidden">
                        <div class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            جاري البحث...
                        </div>
                    </div>
                    <div id="search-no-results" class="p-6 md:p-8 text-center text-gray-500 hidden">
                        <svg class="mx-auto h-10 w-10 md:h-12 md:w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-base md:text-lg font-medium text-gray-900 mb-2">لا توجد نتائج</p>
                        <p class="text-gray-500 text-sm md:text-base">جرب كلمات بحث مختلفة</p>
                    </div>
                </div>

                <!-- Section Filter Dropdown -->
                <div id="section-filter-dropdown" 
                     class="absolute top-full left-0 right-0 bg-white/95 backdrop-blur-sm border border-gray-200 rounded-2xl shadow-2xl mt-2 max-h-80 overflow-y-auto z-[99999] hidden"
                     style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
                    <div id="section-options" class="p-2 md:p-4">
                        <div class="p-2 cursor-pointer hover:bg-gray-100 rounded-lg section-option active" data-section-id="" data-section-name="جميع الأقسام">
                            <div class="flex items-center justify-between">
                                <span>جميع الأقسام</span>
                                <span class="text-xs text-gray-500">الكل</span>
                            </div>
                        </div>
                        <!-- Section options will be populated here -->
                    </div>
                </div>
            </div>
<!--
             // Search Tips 
            <div class="mt-8 text-sm text-white/70">
                <p id="search-tips">💡 نصائح للبحث: استخدم كلمات مفتاحية واضحة، أو ابحث بعناوين الكتب</p>
            </div>
            
             // Scroll Down Indicator 
            <div class="mt-16 animate-bounce">
                <svg class="w-6 h-6 mx-auto text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>
-->            
        </div>
    </div>
</div>

<style>
.section-option {
    transition: all 0.2s ease;
}
.section-option:hover {
    background-color: #f3f4f6;
}
.section-option.active {
    background-color: #dcfce7;
    border: 1px solid #86efac;
}

/* Custom scrollbar for dropdowns */
#search-dropdown::-webkit-scrollbar,
#section-filter-dropdown::-webkit-scrollbar {
    width: 6px;
}

#search-dropdown::-webkit-scrollbar-track,
#section-filter-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#search-dropdown::-webkit-scrollbar-thumb,
#section-filter-dropdown::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

#search-dropdown::-webkit-scrollbar-thumb:hover,
#section-filter-dropdown::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .min-h-screen {
        min-height: 100vh;
    }
}

@media (max-width: 768px) {
    h1 {
        font-size: 2.5rem !important;
        line-height: 1.2 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // عناصر DOM
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    const searchDropdown = document.getElementById('search-dropdown');
    const searchResults = document.getElementById('search-results');
    const searchLoading = document.getElementById('search-loading');
    const searchNoResults = document.getElementById('search-no-results');
    const searchTips = document.getElementById('search-tips');
    
    // أزرار نوع البحث
    const authorsBtn = document.getElementById('search-authors-btn');
    const booksBtn = document.getElementById('search-books-btn');
    const searchTypeBtns = document.querySelectorAll('.search-type-btn');
    
    // فلتر الأقسام
    const sectionFilterContainer = document.getElementById('section-filter-container');
    const sectionFilterBtn = document.getElementById('section-filter-btn');
    const sectionFilterDropdown = document.getElementById('section-filter-dropdown');
    const sectionOptions = document.getElementById('section-options');
    
    // متغيرات
    let currentSearchType = 'books'; // الافتراضي: عناوين الكتب
    let searchTimeout;
    let currentSectionId = '';
    let currentSectionName = 'جميع الأقسام';
    let sectionsData = [];
    
    // تحميل الأقسام عند بدء التشغيل
    loadBookSections();
    
    // تغيير نوع البحث
    authorsBtn.addEventListener('click', () => setSearchType('authors'));
    booksBtn.addEventListener('click', () => setSearchType('books'));
    
    // عرض/إخفاء قائمة الأقسام عند النقر على الزر
    sectionFilterBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (currentSearchType === 'books') {
            sectionFilterDropdown.classList.toggle('hidden');
            searchDropdown.classList.add('hidden');
        }
    });
    
    // البحث عند الكتابة
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            hideDropdown();
            return;
        }
        
        showLoading();
        
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });
    
    // البحث عند الضغط على الزر
    searchBtn.addEventListener('click', function() {
        const query = searchInput.value.trim();
        if (query.length >= 2) {
            performSearch(query);
        }
    });
    
    // البحث عند الضغط على Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query.length >= 2) {
                performSearch(query);
            }
        }
    });
    
    // إخفاء القوائم عند النقر خارجها
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.max-w-2xl')) {
            hideDropdown();
            hideSectionFilter();
        }
    });
    
    // منع إخفاء القوائم عند التمرير (تم إزالة هذه الوظيفة)
    // window.addEventListener('scroll', function() {
    //     hideDropdown();
    //     hideSectionFilter();
    // });
    
    // إخفاء القوائم عند الضغط على Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideDropdown();
            hideSectionFilter();
        }
    });
    
    // تحديد نوع البحث
    function setSearchType(type) {
        currentSearchType = type;
        
        // تحديث أزرار نوع البحث
        searchTypeBtns.forEach(btn => {
            btn.classList.remove('active', 'bg-white', 'text-green-800');
            btn.classList.add('bg-white/20', 'backdrop-blur-sm', 'text-white', 'border-white/50');
        });
        
        if (type === 'authors') {
            authorsBtn.classList.add('active', 'bg-white', 'text-green-800');
            authorsBtn.classList.remove('bg-white/20', 'backdrop-blur-sm', 'text-white', 'border-white/50');
            searchInput.placeholder = 'إبحث في أسماء المؤلفين ...';
            searchTips.innerHTML = '💡 نصائح للبحث: استخدم أسماء المؤلفين أو أجزاء منها';
            sectionFilterContainer.style.display = 'none';
        } else {
            booksBtn.classList.add('active', 'bg-white', 'text-green-800');
            booksBtn.classList.remove('bg-white/20', 'backdrop-blur-sm', 'text-white', 'border-white/50');
            searchInput.placeholder = 'إبحث في عناوين الكتب ...';
            searchTips.innerHTML = '💡 نصائح للبحث: استخدم كلمات مفتاحية واضحة، أو ابحث بعناوين الكتب';
            sectionFilterContainer.style.display = 'flex';
        }
        
        // تعيين "جميع الأقسام" كافتراضي عند التبديل إلى البحث في الكتب
        if (type === 'books') {
            currentSectionId = '';
            currentSectionName = 'جميع الأقسام';
        }
        
        // إعادة البحث إذا كان هناك نص
        const query = searchInput.value.trim();
        if (query.length >= 2) {
            performSearch(query);
        } else {
            hideDropdown();
        }
    }
    
    // تحميل أقسام الكتب
    async function loadBookSections() {
        try {
            const response = await fetch('/api/search-all/sections');
            const data = await response.json();
            
            if (data.success) {
                sectionsData = data.data;
                populateSectionOptions(data.data);
            }
        } catch (error) {
            console.error('خطأ في تحميل الأقسام:', error);
        }
    }
    
    // ملء قائمة الأقسام
    function populateSectionOptions(sections) {
        // إزالة الخيارات السابقة باستثناء "جميع الأقسام"
        const defaultOption = sectionOptions.querySelector('[data-section-id=""]');
        
        // إزالة جميع الخيارات الأخرى
        while (sectionOptions.firstChild) {
            sectionOptions.removeChild(sectionOptions.firstChild);
        }
        
        // إضافة خيار "جميع الأقسام" مرة أخرى
        sectionOptions.appendChild(defaultOption);
        
        // إضافة باقي الأقسام
        sections.forEach(section => {
            const option = document.createElement('div');
            option.className = 'p-2 cursor-pointer hover:bg-gray-100 rounded-lg section-option';
            option.setAttribute('data-section-id', section.id);
            option.setAttribute('data-section-name', section.name);
            
            option.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${section.name}</span>
                    <span class="text-xs text-gray-500">${section.books_count}</span>
                </div>
            `;
            
            option.addEventListener('click', function() {
                currentSectionId = section.id;
                currentSectionName = section.name;
                
                // إزالة التحديد من الخيارات الأخرى
                document.querySelectorAll('.section-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                
                // إضافة التحديد للخيار الحالي
                this.classList.add('active');
                
                hideSectionFilter();
                
                // إعادة البحث إذا كان هناك نص
                const query = searchInput.value.trim();
                if (query.length >= 2) {
                    performSearch(query);
                }
            });
            
            sectionOptions.appendChild(option);
        });
    }
    
    // تحديد خيار "جميع الأقسام"
    const allSectionsOption = sectionOptions.querySelector('[data-section-id=""]');
    allSectionsOption.addEventListener('click', function() {
        currentSectionId = '';
        currentSectionName = 'جميع الأقسام';
        
        // إزالة التحديد من الخيارات الأخرى
        document.querySelectorAll('.section-option').forEach(opt => {
            opt.classList.remove('active');
        });
        
        // إضافة التحديد للخيار الحالي
        this.classList.add('active');
        
        hideSectionFilter();
        
        // إعادة البحث إذا كان هناك نص
        const query = searchInput.value.trim();
        if (query.length >= 2) {
            performSearch(query);
        }
    });
    
    // إخفاء قائمة الأقسام
    function hideSectionFilter() {
        sectionFilterDropdown.classList.add('hidden');
    }
    
    // تنفيذ البحث
    async function performSearch(query) {
        try {
            // التمرير التلقائي لمربع البحث عند بدء البحث
            scrollToSearchBox();
            
            let url = '';
            const params = new URLSearchParams({
                q: query,
                limit: 15
            });
            
            if (currentSearchType === 'authors') {
                url = '/api/search-all/authors';
            } else {
                url = '/api/search-all/books';
                if (currentSectionId) {
                    params.append('section_id', currentSectionId);
                }
            }
            
            const response = await fetch(`${url}?${params}`);
            const data = await response.json();
            
            hideLoading();
            
            if (data.success && data.data.length > 0) {
                displayResults(data.data);
            } else {
                showNoResults();
            }
        } catch (error) {
            console.error('خطأ في البحث:', error);
            hideLoading();
            showNoResults();
        }
    }
    
    // وظيفة التمرير التلقائي لمربع البحث
    function scrollToSearchBox() {
        const searchBox = document.querySelector('.max-w-2xl');
        if (searchBox) {
            const offsetTop = searchBox.offsetTop - 100; // ترك مساحة 100px من الأعلى
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    }
    
    // عرض النتائج
    function displayResults(results) {
        searchResults.innerHTML = '';
        
        results.forEach(result => {
            const resultElement = createResultElement(result);
            searchResults.appendChild(resultElement);
        });
        
        showDropdown();
    }
    
    // إنشاء عنصر نتيجة
    function createResultElement(result) {
        const div = document.createElement('div');
        div.className = 'p-2 md:p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer transition-colors duration-200';
        
        if (result.type === 'author') {
            div.innerHTML = `
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs md:text-sm font-medium text-gray-900 truncate">${result.name}</h3>
                        ${result.years ? `<p class="text-xs text-gray-500">${result.years}</p>` : ''}
                        ${result.madhhab ? `<p class="text-xs text-blue-600">${result.madhhab}</p>` : ''}
                        <p class="text-xs text-gray-400">${result.books_count} كتاب</p>
                    </div>
                </div>
            `;
            
            div.addEventListener('click', () => {
                window.location.href = `/authors/${result.id}/details`;
            });
        } else {
            div.innerHTML = `
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 md:w-5 md:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs md:text-sm font-medium text-gray-900 truncate">${result.title}</h3>
                        ${result.authors_text ? `<p class="text-xs text-gray-500">بقلم: ${result.authors_text}</p>` : ''}
                        <p class="text-xs text-blue-600">${result.section.name}</p>
                        <div class="flex items-center space-x-2 space-x-reverse text-xs text-gray-400 mt-1">
                            ${result.pages_count ? `<span>${result.pages_count} صفحة</span>` : ''}
                            ${result.volumes_count > 1 ? `<span>• ${result.volumes_count} مجلد</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
            
            div.addEventListener('click', () => {
                window.location.href = `/book/${result.id}`;
            });
        }
        
        return div;
    }
    
    // إظهار القائمة المنسدلة
    function showDropdown() {
        searchResults.classList.remove('hidden');
        searchLoading.classList.add('hidden');
        searchNoResults.classList.add('hidden');
        searchDropdown.classList.remove('hidden');
    }
    
    // إخفاء القائمة المنسدلة
    function hideDropdown() {
        searchDropdown.classList.add('hidden');
    }
    
    // إظهار التحميل
    function showLoading() {
        searchResults.classList.add('hidden');
        searchLoading.classList.remove('hidden');
        searchNoResults.classList.add('hidden');
        searchDropdown.classList.remove('hidden');
    }
    
    // إخفاء التحميل
    function hideLoading() {
        searchLoading.classList.add('hidden');
    }
    
    // إظهار عدم وجود نتائج
    function showNoResults() {
        searchResults.classList.add('hidden');
        searchLoading.classList.add('hidden');
        searchNoResults.classList.remove('hidden');
        searchDropdown.classList.remove('hidden');
    }
});
</script>
