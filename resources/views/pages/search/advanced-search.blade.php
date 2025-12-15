@extends('layouts.app')

@section('content')
<div class="flex flex-col h-screen bg-gray-50" dir="rtl">
    <!-- Global Header -->
    <div class="z-50 relative">
        <x-layout.header />
    </div>

    <!-- Search Header Section -->
    <div class="flex-none z-40 bg-white border-b border-gray-200 shadow-sm relative">
        <x-search.header />
    </div>

    <!-- Main Content Area (Sidebar + Preview) -->
    <div class="flex flex-1 overflow-hidden relative z-0">
        
        <!-- Right Sidebar (Search Results) -->
        <aside class="w-80 md:w-96 flex-none bg-white border-l border-gray-200 shadow-lg z-10 hidden lg:block overflow-hidden relative" id="search-sidebar">
            <x-search.results-sidebar />
        </aside>

        <!-- Main Preview Pane -->
        <main class="flex-1 overflow-hidden relative bg-gray-100 flex flex-col justify-center items-center">
            <x-search.preview-pane />
        </main>

        <!-- Mobile Toggle Button (Visible only on small screens) -->
        <button id="mobile-sidebar-toggle" 
                class="lg:hidden absolute bottom-6 right-6 z-30 p-3 rounded-full bg-green-600 text-white shadow-lg hover:bg-green-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>
</div>

{{-- Mobile Sidebar Overlay --}}
<div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden">
    <div class="absolute inset-y-0 right-0 w-80 bg-white transform translate-x-full transition-transform duration-300" id="mobile-sidebar-panel">
        <div class="h-full flex flex-col">
            <div class="p-4 border-b flex justify-between items-center">
                <h3 class="font-bold">نتائج البحث</h3>
                <button id="mobile-sidebar-close" class="p-2 text-gray-500 hover:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-hidden relative">
                <x-search.results-sidebar />
            </div>
        </div>
    </div>
</div>

<script>
    // Basic mobile sidebar toggle
    const toggleBtn = document.getElementById('mobile-sidebar-toggle');
    const overlay = document.getElementById('mobile-sidebar-overlay');
    const panel = document.getElementById('mobile-sidebar-panel');
    const closeBtn = document.getElementById('mobile-sidebar-close');

    function openSidebar() {
        overlay.classList.remove('hidden');
        setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    }

    function closeSidebar() {
        panel.classList.add('translate-x-full');
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }

    if(toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if(overlay) overlay.addEventListener('click', (e) => {
        if(e.target === overlay) closeSidebar();
    });
</script>
@endsection
