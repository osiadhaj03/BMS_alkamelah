@props(['book' => null])

<style>
    /* Scoped styles for the book header */
    .book-header {
        background-image: url('/images/backgrond_islamic.png');
        background-size: auto;
        background-repeat: repeat;
        background-position: center top;
        position: relative;
        padding: 0.5rem 0.75rem; /* Compact padding for mobile */
        border-bottom: 1px solid var(--border-color);
    }
    
    /* Desktop header padding */
    @media (min-width: 1024px) {
        .book-header {
            padding: 1.5rem 2rem;
        }
    }
    
    .book-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background-color: rgba(255, 255, 255, 0);
        z-index: 0;
    }
    .book-header > * {
        position: relative;
        z-index: 1;
    }

    .breadcrumbs {
        display: none; /* Hidden by default (mobile) */
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
    }
    
    @media (min-width: 1024px) {
        .breadcrumbs {
            display: flex;
        }
    }
    .breadcrumbs .separator {
        color: #cbd5e1;
    }
    .breadcrumbs a {
        color: var(--text-secondary);
        text-decoration: none;
        transition: color 0.2s;
    }
    .breadcrumbs a:hover {
        color: var(--accent-color);
    }
    .breadcrumbs .current {
        color: var(--accent-color);
        font-weight: 600;
    }

    .header-main-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem; /* Tight gap for mobile */
        flex-wrap: nowrap; /* Single line on mobile */
    }
    
    @media (min-width: 1024px) {
        .header-main-row {
            gap: 2rem;
            flex-wrap: wrap;
        }
    }

    .book-identity {
        display: flex;
        align-items: center;
        gap: 0.5rem; /* Tighter gap for mobile */
        flex: 1;
        min-width: 0; /* Allow truncation */
    }
    
    @media (min-width: 1024px) {
        .book-identity {
            gap: 1rem;
            flex: unset;
        }
    }

    /* Hide book icon on mobile */
    .book-icon-svg {
        display: none;
    }
    
    @media (min-width: 1024px) {
        .book-icon-svg {
            display: block;
            width: 48px;
            height: 48px;
        }
    }

    .header-title {
        min-width: 0; /* Enable truncation */
    }
    
    .header-title h1 {
        font-size: 0.95rem; /* Smaller on mobile */
        font-weight: 600;
        color: var(--text-main);
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px; /* Limit width for truncation on mobile */
    }
    
    @media (min-width: 1024px) {
        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
            max-width: none;
        }
    }
    
    /* Hide author name on mobile */
    .header-title span {
        display: none;
    }
    
    @media (min-width: 1024px) {
        .header-title span {
            font-size: 0.9rem;
            color: var(--text-secondary);
            display: block;
        }
    }

    /* Center: Search */
    .search-container {
        display: none; /* Hidden by default (mobile) */
    }
    
    @media (min-width: 1024px) {
        .search-container {
            flex: 1;
            max-width: 600px;
            display: flex;
            align-items: center;
            background: var(--bg-paper);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 4px;
            box-shadow: var(--shadow-soft);
            position: relative;
        }
        
        #header-search-mobile-btn {
            display: none !important;
        }
    }

    .search-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.5rem 1rem;
        font-family: var(--font-ui);
        color: var(--text-main);
        outline: none;
    }

    .actions-container {
        position: relative;
    }

    .btn-menu, .btn-filter, .btn-search-action, .btn-more {
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        cursor: pointer;
        color: var(--text-secondary);
        transition: all 0.2s;
        border-radius: 8px;
    }

    /* Smaller menu button on mobile */
    .btn-menu { 
        width: 32px; 
        height: 32px;
        flex-shrink: 0;
    }
    .btn-menu:hover { background: var(--bg-hover); color: var(--text-main); }
    
    @media (min-width: 1024px) {
        .btn-menu { 
            width: 40px; 
            height: 40px; 
        }
    }

    .btn-filter {
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--bg-hover);
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-main);
        border-radius: 8px;
    }
    .btn-filter:hover { background: #e5e7eb; }

    .btn-search-action {
        width: 36px;
        height: 36px;
        background: var(--accent-color);
        color: white;
        border-radius: 8px;
    }
    .btn-search-action:hover { background: var(--accent-hover); }

    .btn-more {
        width: 40px;
        height: 40px;
        border: 1px solid var(--border-color);
        background: var(--bg-paper);
    }
    .btn-more:hover { border-color: var(--accent-color); color: var(--accent-color); }

    .dropdown-menu {
        position: absolute;
        top: 120%;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease;
        background: var(--bg-paper);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: var(--shadow-dropdown);
        min-width: 220px;
        padding: 0.5rem;
        z-index: 50;
    }
    
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* RTL support */
    [dir="rtl"] .dropdown-menu {
        right: 0;
        left: auto;
    }
    [dir="ltr"] .dropdown-menu {
        left: 0;
        right: auto;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 0.75rem 1rem;
        border: none;
        background: transparent;
        color: var(--text-main);
        font-family: var(--font-ui);
        font-size: 0.95rem;
        cursor: pointer;
        border-radius: 8px;
        transition: background 0.2s;
        text-align: start;
    }
    .dropdown-item:hover {
        background: var(--bg-hover);
    }
    .dropdown-item svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
    }

    .dropdown-divider {
        height: 1px;
        background: var(--border-color);
        margin: 0.5rem 0;
    }
</style>

<header class="book-header">
    <!-- Breadcrumbs -->
    <nav class="breadcrumbs">
        <a href="{{ url('/') }}">الرئيسية</a>
        <span class="separator">/</span>
        <a href="#">أقسام الكتاب</a>
        <span class="separator">/</span>
        <a href="#">{{ $book?->bookSection?->name ?? 'أصول الفقه' }}</a>
        <span class="separator">/</span>
        <span class="current">{{ Str::limit($book?->title ?? 'آداب الفتوى', 30) }}</span>
    </nav>

    <div class="header-main-row">
        <!-- Identity -->
        <div class="book-identity">
            <button class="btn-menu" id="menu-toggle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="book-icon-svg">
                <img src="/images/icon_islamic.png" alt="أيقونة إسلامية" width="48" height="48" style="display: block; margin: 0 auto;" />
            </div>
            <div class="header-title">
                <h1>{{ $book?->title ?? 'آداب الفتوى' }}</h1>
                <span>{{ $book?->authors?->first()?->full_name ?? 'الإمام النووي' }}</span>
            </div>
        </div>
        
        <!-- Mobile Search Icon (Visible only on mobile) -->
        <button class="btn-menu lg:hidden" id="header-search-mobile-btn" style="margin-right: auto; color: var(--accent-color);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </button>

        <!-- Search Bar (New) -->
        <div class="search-container">
            <div class="actions-container">
                <button class="btn-filter" id="btn-filter">
                    <span>تصفية</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M6 12h12M10 18h4"/></svg>
                </button>
                <!-- Filter Dropdown -->
                <div class="dropdown-menu filter-dropdown" id="filter-menu">
                    <button class="dropdown-item">كل الكتاب</button>
                    <button class="dropdown-item">العناوين فقط</button>
                    <button class="dropdown-item">الصفحة الحالية</button>
                </div>
            </div>
            
            <!-- Dynamic Search Component -->
            <div class="flex-1 relative">
                <x-book.book-search :book="$book" />
            </div>
        </div>

        <!-- More Actions (Three Dots) -->
        <div class="actions-container">

        </div>
    </div>
</header>

<!-- Mobile Search Sidebar Overlay -->
<div id="mobile-search-overlay" 
     class="fixed inset-0 z-50 hidden lg:hidden"
     style="background-color: rgba(0,0,0,0.5);">
    
    <!-- Sidebar Panel -->
    <div id="mobile-search-panel"
         class="absolute top-0 right-0 h-full w-80 max-w-full bg-white shadow-xl transform transition-transform duration-300"
         style="background-color: var(--bg-paper);">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b" style="border-color: var(--border-color);">
            <h3 class="text-lg font-bold" style="color: var(--text-main); font-family: var(--font-ui);">
                البحث في الكتاب
            </h3>
            <button id="close-mobile-search" 
                    class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Search Component -->
        <div class="p-4">
            <x-book.book-search :book="$book" :inline="true" />
        </div>
        
        <!-- Quick Tips 
        <div class="px-4 py-3 mx-4 rounded-lg" style="background-color: var(--accent-light);">
            <p class="text-sm" style="color: var(--accent-color); font-family: var(--font-ui);">
                💡 اكتب كلمة البحث واضغط على النتيجة للانتقال للصفحة
            </p>
        </div>
        -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Filter Menu
        const btnFilter = document.getElementById('btn-filter');
        const filterMenu = document.getElementById('filter-menu');
        
        if(btnFilter && filterMenu) {
            btnFilter.addEventListener('click', function(e) {
                e.stopPropagation();
                filterMenu.classList.toggle('show');
                // Close other menu
                if(moreMenu) moreMenu.classList.remove('show');
            });
        }

        // Toggle More Menu
        const btnMore = document.getElementById('btn-more-toggle');
        const moreMenu = document.getElementById('more-menu');
        
        if(btnMore && moreMenu) {
            btnMore.addEventListener('click', function(e) {
                e.stopPropagation();
                moreMenu.classList.toggle('show');
                // Close other menu
                if(filterMenu) filterMenu.classList.remove('show');
            });
        }

        // Close menus when clicking outside
        document.addEventListener('click', function() {
            if(filterMenu) filterMenu.classList.remove('show');
            if(moreMenu) moreMenu.classList.remove('show');
        });

        // Prevent closing when clicking inside the menu
        if(filterMenu) {
            filterMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        if(moreMenu) {
            moreMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // Mobile Search Sidebar
        const mobileSearchBtn = document.getElementById('header-search-mobile-btn');
        const mobileSearchOverlay = document.getElementById('mobile-search-overlay');
        const mobileSearchPanel = document.getElementById('mobile-search-panel');
        const closeMobileSearch = document.getElementById('close-mobile-search');
        
        function openMobileSearch() {
            mobileSearchOverlay.classList.remove('hidden');
            setTimeout(() => {
                mobileSearchPanel.style.transform = 'translateX(0)';
            }, 10);
            // Focus on search input
            setTimeout(() => {
                const searchInput = mobileSearchPanel.querySelector('input[type="text"]');
                if (searchInput) searchInput.focus();
            }, 300);
        }
        
        function closeMobileSearchPanel() {
            mobileSearchPanel.style.transform = 'translateX(100%)';
            setTimeout(() => {
                mobileSearchOverlay.classList.add('hidden');
            }, 300);
        }
        
        if (mobileSearchBtn) {
            mobileSearchBtn.addEventListener('click', openMobileSearch);
        }
        
        if (closeMobileSearch) {
            closeMobileSearch.addEventListener('click', closeMobileSearchPanel);
        }
        
        // Close when clicking overlay background
        if (mobileSearchOverlay) {
            mobileSearchOverlay.addEventListener('click', function(e) {
                if (e.target === mobileSearchOverlay) {
                    closeMobileSearchPanel();
                }
            });
        }
        
        // Set initial state
        if (mobileSearchPanel) {
            mobileSearchPanel.style.transform = 'translateX(100%)';
        }
    });
</script>
