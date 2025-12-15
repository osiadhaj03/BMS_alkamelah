<div class="px-4 py-3 md:px-6 md:py-4 bg-white" x-data="{ 
    settingsOpen: false, 
    searchType: 'flexible', // exact, flexible, morphological, semantic
    wordOrder: 'any', // consecutive, paragraph, any
    query: ''
}">
    <div class="max-w-7xl mx-auto flex flex-col gap-4">
        
        <!-- Top Row: Logo & Main Search Input -->
        <div class="flex items-center gap-4">

            <!-- Search Bar Container -->
            <div class="flex-1 relative">
                <div class="relative flex items-center bg-gray-100 border border-gray-300 rounded-xl focus-within:ring-2 focus-within:ring-green-500 focus-within:border-green-500 transition-shadow shadow-sm z-30">
                    
                    <!-- Search Icon -->
                    <div class="pl-3 pr-4 text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <!-- Input -->
                    <input type="text" 
                           x-model="query"
                           class="w-full bg-transparent border-none py-3 text-lg text-gray-800 placeholder-gray-400 focus:ring-0"
                           placeholder="ابحث عن كلمة، عبارة، أو موضوع..."
                           autofocus>

                    <!-- Settings Toggle Button -->
                    <button @click="settingsOpen = !settingsOpen" 
                            @click.outside="settingsOpen = false"
                            class="px-4 py-2 text-gray-600 hover:text-green-700 hover:bg-gray-200 border-r border-gray-300 flex items-center gap-2 transition-colors relative h-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': settingsOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <!-- Search Button -->
                    <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-l-xl font-medium transition-colors">
                        بحث
                    </button>
                </div>

                <!-- Settings Dropdown Menu (Floating) -->
                <div x-show="settingsOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     @click.outside="settingsOpen = false"
                     class="absolute top-full left-0 mt-2 w-full md:w-2/3 lg:w-[400px] bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden"
                     style="left: 0;">
                    
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Search Type -->
                        <div class="space-y-3">
                             <h4 class="font-bold text-gray-700 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">
                                نوع البحث
                            </h4>
                            <div class="flex flex-col gap-1">
                                <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <div class="relative flex items-center">
                                        <input type="radio" name="searchType" value="exact" x-model="searchType" class="peer h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    </div>
                                    <span class="text-sm text-gray-700 group-hover:text-green-700 font-medium">البحث المطابق</span>
                                </label>
                                
                                <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="searchType" value="flexible" x-model="searchType" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-green-700 font-medium">البحث الغير مطابق</span>
                                </label>

                                <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="searchType" value="morphological" x-model="searchType" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-green-700 font-medium">البحث الصرفي</span>
                                </label>

                            </div>
                        </div>

                        <!-- Word Order -->
                        <div class="space-y-3">
                             <h4 class="font-bold text-gray-700 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">
                                ترتيب الكلمات
                            </h4>
                            <div class="flex flex-col gap-1">
                                <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="wordOrder" value="consecutive" x-model="wordOrder" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-blue-700 font-medium">كلمات متتالية</span>
                                </label>

                                <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="wordOrder" value="paragraph" x-model="wordOrder" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-blue-700 font-medium">في نفس الفقرة</span>
                                </label>

                                <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors group">
                                    <input type="radio" name="wordOrder" value="any" x-model="wordOrder" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700 group-hover:text-blue-700 font-medium">أي ترتيب</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dropdown Footer -->
                    <div class="bg-gray-50 px-4 py-2 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-400">
                        <button @click="settingsOpen = false" class="text-gray-500 hover:text-gray-700 font-bold">إغلاق</button>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Group (Filter / Sort / Help) -->
            <div class="flex items-center gap-2">
                <button class="p-2.5 text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-200" title="تصفية النتائج">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>
                <button class="p-2.5 text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-200" title="ترتيب النتائج">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                </button>
                <button class="p-2.5 text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-200" title="مساعدة">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
