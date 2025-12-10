<div class="fixed top-0 left-0 right-0 bg-white shadow-sm border-b z-50">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-600 mb-3" dir="rtl" style="font-family: Tajawal;">
            <ol class="flex items-center space-x-2 space-x-reverse">
                <li><a href="/" class="hover:text-green-600">الرئيسية</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="#" class="hover:text-green-600">أقسام الكتاب</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="#" class="hover:text-green-600">{{ $book->section->name ?? 'القسم' }}</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-800">{{ $book->title }}</li>
            </ol>
        </nav>

        <!-- Book Title and Controls -->
        <div class="flex items-center justify-between" dir="rtl">
            <!-- Book Title with Icon -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-8">
                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="48" fill="#e8f3f0" stroke="#16a34a" stroke-width="2"/>
                        <g transform="translate(50, 50)">
                            <!-- Islamic Pattern -->
                            <circle cx="0" cy="0" r="35" fill="none" stroke="#16a34a" stroke-width="1" opacity="0.3"/>
                            <circle cx="0" cy="0" r="28" fill="none" stroke="#16a34a" stroke-width="1" opacity="0.3"/>
                            <g stroke="#16a34a" stroke-width="1.5" fill="none">
                                <line x1="0" y1="-20" x2="0" y2="20"/>
                                <line x1="-20" y1="0" x2="20" y2="0"/>
                                <line x1="-14" y1="-14" x2="14" y2="14"/>
                                <line x1="-14" y1="14" x2="14" y2="-14"/>
                            </g>
                            <circle cx="0" cy="0" r="3" fill="#16a34a"/>
                        </g>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-green-700" style="font-family: Tajawal;">
                    {{ $book->title }}
                </h1>
            </div>

            <!-- Search and Controls -->
            <div class="flex items-center gap-4">
                <!-- Search in Book -->
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="بحث في الكتاب..." 
                        class="w-64 px-4 py-2 pr-10 text-sm border border-gray-300 rounded-full focus:outline-none focus:border-green-600 text-right"
                        dir="rtl"
                        style="font-family: Tajawal;"
                    >
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-green-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Reading Controls -->
                <div class="flex items-center gap-2">
                    <button class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg" title="مشاركة">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                    </button>
                    <button class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg" title="ملء الشاشة">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </button>
                    <button class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg" title="طباعة">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>