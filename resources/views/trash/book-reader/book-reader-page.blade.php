{{--
    Book Reader Page - الصفحة الرئيسية لقارئ الكتب
    
    مستوحاة من: تراث (Turath.io) + كود Google
--}}

@push('styles')
<style>
    @include('livewire.book-reader.partials.reader-styles')
</style>
@endpush

<div 
    class="book-reader"
    x-data="bookReader({
        bookId: @js($bookId),
        pageNumber: @js($pageNumber),
        totalPages: @js($this->totalPages),
        theme: @js($theme),
        fontSize: @js($fontSize),
        showSidebar: @js($showSidebar),
    })"
    :data-theme="theme"
    dir="rtl"
>
    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Mini Sidebar (الشريط الجانبي الصغير - أيقونات) --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <aside class="mini-sidebar">
        {{-- أيقونة القراءة (نشطة) --}}
        <button class="mini-icon active" title="القراءة">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
            </svg>
        </button>
        
        {{-- أيقونة الفهرس --}}
        <button 
            class="mini-icon" 
            :class="{ 'active': $wire.showSidebar }"
            wire:click="toggleSidebar"
            title="الفهرس"
        >
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
            </svg>
        </button>
        
        {{-- أيقونة البحث --}}
        <button 
            class="mini-icon"
            :class="{ 'active': $wire.showSearchModal }"
            @click="$wire.showSearchModal = true"
            title="البحث"
        >
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
        </button>
        
        {{-- أيقونة الإعدادات --}}
        <button 
            class="mini-icon"
            :class="{ 'active': $wire.showSettingsModal }"
            @click="$wire.showSettingsModal = true"
            title="الإعدادات"
        >
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
            </svg>
        </button>
        
        {{-- فاصل --}}
        <div class="mini-sidebar-divider"></div>
        
        {{-- أيقونة الثيم --}}
        <button 
            class="mini-icon"
            wire:click="toggleTheme"
            title="تبديل الوضع"
        >
            <template x-if="theme === 'light'">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/>
                </svg>
            </template>
            <template x-if="theme === 'dark'">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0 .39-.39.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/>
                </svg>
            </template>
            <template x-if="theme === 'sepia'">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 22C6.49 22 2 17.51 2 12S6.49 2 12 2s10 4.04 10 9c0 3.31-2.69 6-6 6h-1.77c-.28 0-.5.22-.5.5 0 .12.05.23.13.33.41.47.64 1.06.64 1.67A2.5 2.5 0 0 1 12 22zm0-18c-4.41 0-8 3.59-8 8s3.59 8 8 8c.28 0 .5-.22.5-.5a.54.54 0 0 0-.14-.35c-.41-.46-.63-1.05-.63-1.65a2.5 2.5 0 0 1 2.5-2.5H16c2.21 0 4-1.79 4-4 0-3.86-3.59-7-8-7z"/>
                </svg>
            </template>
        </button>
    </aside>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Main Container --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="reader-main">
        
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- Header --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @include('livewire.book-reader.partials.header')
        
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- Workspace (Sidebar + Content) --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="reader-workspace">
            
            {{-- Overlay for mobile --}}
            <div 
                class="sidebar-overlay"
                x-show="$wire.showSidebar"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="$wire.showSidebar = false"
                x-cloak
            ></div>
            
            {{-- Sidebar (Table of Contents) --}}
            @include('livewire.book-reader.partials.sidebar-toc')
            
            {{-- Content Area --}}
            <main class="reader-content" id="reader-content">
                @include('livewire.book-reader.partials.content-area')
            </main>
            
        </div>
        
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- Navigation Bar --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @include('livewire.book-reader.partials.navigation-bar')
        
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Modals --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @include('livewire.book-reader.partials.search-modal')
    @include('livewire.book-reader.partials.settings-modal')

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- Loading Overlay --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div 
        class="reader-loading"
        x-show="$wire.isLoading"
        x-transition
    >
        <div class="loading-spinner">
            <svg class="spinner" viewBox="0 0 50 50">
                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
            </svg>
            <span>جارٍ التحميل...</span>
        </div>
    </div>
</div>
