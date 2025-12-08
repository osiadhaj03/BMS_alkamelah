{{-- Header - شريط الأدوات العلوي --}}

<header class="reader-header">
    {{-- الجانب الأيمن: عنوان الكتاب --}}
    <div class="header-right">
        {{-- زر إظهار/إخفاء الفهرس --}}
        <button 
            class="menu-toggle-btn"
            wire:click="toggleSidebar"
            title="{{ $showSidebar ? 'إخفاء الفهرس' : 'إظهار الفهرس' }}"
        >
            @if($showSidebar)
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            @else
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                </svg>
            @endif
        </button>
        
        {{-- معلومات الكتاب --}}
        <div class="header-book-info">
            <h1 class="book-title">{{ $book->title }}</h1>
            @if($book->authors->isNotEmpty())
                <span class="book-author">
                    {{ $book->authors->first()->name }}
                    @if($book->authors->first()->death_year)
                        (ت {{ $book->authors->first()->death_year }}هـ)
                    @endif
                </span>
            @endif
        </div>
    </div>
    
    {{-- الجانب الأيسر: أدوات --}}
    <div class="header-left">
        {{-- شريط الأدوات (كبسولة) --}}
        <div class="toolbar-pill">
            {{-- بحث --}}
            <button 
                class="btn-icon"
                @click="$wire.showSearchModal = true"
                title="بحث (Ctrl+F)"
            >
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </button>
            
            <div class="toolbar-divider"></div>
            
            {{-- تصغير الخط --}}
            <button 
                class="btn-icon btn-text"
                wire:click="decreaseFontSize"
                title="تصغير الخط"
            >
                أ-
            </button>
            
            {{-- تكبير الخط --}}
            <button 
                class="btn-icon btn-text"
                wire:click="increaseFontSize"
                title="تكبير الخط"
            >
                أ+
            </button>
            
            <div class="toolbar-divider"></div>
            
            {{-- تبديل الثيم --}}
            <button 
                class="btn-icon"
                wire:click="toggleTheme"
                title="تبديل الوضع"
            >
                @if($theme === 'light')
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/>
                    </svg>
                @elseif($theme === 'dark')
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1z"/>
                    </svg>
                @else
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.49 22 2 17.51 2 12S6.49 2 12 2s10 4.04 10 9c0 3.31-2.69 6-6 6h-1.77c-.28 0-.5.22-.5.5 0 .12.05.23.13.33.41.47.64 1.06.64 1.67A2.5 2.5 0 0 1 12 22z"/>
                    </svg>
                @endif
            </button>
            
            {{-- إعدادات --}}
            <button 
                class="btn-icon"
                @click="$wire.showSettingsModal = true"
                title="الإعدادات"
            >
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
            
            <div class="toolbar-divider"></div>
            
            {{-- مشاركة --}}
            <button 
                class="btn-icon btn-primary"
                x-data
                @click="shareBook()"
                title="مشاركة"
            >
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/>
                </svg>
                <span class="btn-text-label">مشاركة</span>
            </button>
        </div>
    </div>
</header>

<script>
    function shareBook() {
        if (navigator.share) {
            navigator.share({
                title: document.querySelector('.book-title')?.textContent || 'كتاب',
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('تم نسخ الرابط');
            });
        }
    }
</script>
