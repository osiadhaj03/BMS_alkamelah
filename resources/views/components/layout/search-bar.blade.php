<!-- Search Type Buttons - Above Search -->
        <div class="flex justify-center gap-4 mb-6">
            <button class="bg-white text-gray-700 border-2 border-gray-400 px-10 py-3 rounded-full font-medium text-base transition-colors hover:bg-white hover:border-gray-400 hover:text-gray-700" style="font-family: Tajawal;">
                المؤلفين
            </button>
            <button class="bg-green-700 text-white px-10 py-3 rounded-full font-medium text-base transition-colors hover:bg-green-800" style="font-family: Tajawal;">
                محتوى الكتب
            </button>
            <button class="bg-white text-gray-700 border-2 border-gray-400 px-10 py-3 rounded-full font-medium text-base transition-colors hover:bg-white hover:border-gray-400 hover:text-gray-700" style="font-family: Tajawal;">
                عناوين الكتب
            </button>
        </div>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto">
            <div class="relative" dir="rtl">
                <input type="text" 
                       placeholder="ابحث عن كتاب أو مؤلف..." 
                       class="w-full px-6 py-4 pr-20 pl-14 text-lg border-2 border-gray-300 rounded-full focus:outline-none focus:border-green-600 text-right">
                
                <!-- Filter Icon (Right side) -->
                <button class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-green-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </button>
                
                <!-- Search Icon (Left side) -->
                <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-green-600 text-white p-2 rounded-full hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>