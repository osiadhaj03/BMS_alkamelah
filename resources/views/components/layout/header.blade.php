<style>
    .text-sm {
        font-size: 1rem;
        line-height: 0.35rem;
    }

    .border-b-2 {
        border-bottom-width: 2px;
    }

    .rounded-md {
        border-radius: -0.625rem;
    }

    .px-3 {
        padding-left: 0.05rem;
        padding-right: 0.05rem;
    }
</style>

<header class="bg-white shadow-sm border-b" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center">
                    <!-- Temporary text logo until image is added 
                    <span class="text-2xl font-bold text-green-800">المكتبة الكاملة</span> -->
                    <!-- Image logo (uncomment when image is ready) -->
                    <img src="{{ asset('images/المكتبة الكاملة.png') }}" alt="المكتبة الكاملة" class="h-14 w-auto">
                </a>
            </div>

            <!-- Navigation - Centered -->
            <nav class="hidden md:flex space-x-8 space-x-reverse flex-1 justify-center">
                <a href="{{ route('home') }}"
                    class="text-gray-700 hover:text-green-800 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-green-800 transition-colors {{ request()->routeIs('home') ? 'border-green-800 text-green-800' : '' }}">
                    الرئيسية
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('search.static') }}"
                    class="text-gray-700 hover:text-green-800 px-3 py-2  text-sm font-medium border-b-2 border-transparent hover:border-green-800 transition-colors {{ request()->routeIs('search.static') ? 'border-green-800 text-green-800' : '' }}">
                    البحث
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('categories.index') }}"
                    class="text-gray-700 hover:text-green-800 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-green-800 transition-colors {{ request()->routeIs('categories.index') ? 'border-green-800 text-green-800' : '' }}">
                    الأقسام
                </a>

                <span class="text-gray-300">|</span>
                <a href="{{ route('books.index') }}"
                    class="text-gray-700 hover:text-green-800 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-green-800 transition-colors {{ request()->routeIs('books.index') ? 'border-green-800 text-green-800' : '' }}">
                    الكتب
                </a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('authors.index') }}"
                    class="text-gray-700 hover:text-green-800 px-3 py-2  text-sm font-medium border-b-2 border-transparent hover:border-green-800 transition-colors {{ request()->routeIs('authors.index') ? 'border-green-800 text-green-800' : '' }}">
                    المؤلفين
                </a>
                <span class="text-gray-300">|</span>
                <a href="#"
                    class="text-gray-700 hover:text-green-800 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-green-800 transition-colors">
                    عن المكتبة
                </a>
            </nav>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button"
                    class="text-gray-700 hover:text-green-800 focus:outline-none focus:text-green-800">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>