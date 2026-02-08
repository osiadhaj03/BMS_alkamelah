<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        {{-- SEO Meta Tags --}}
        <meta name="title" content="البحث الفوري في الكتب الإسلامية">
        <meta name="description" content="ابحث في آلاف الكتب الإسلامية والعربية مع نظام بحث متقدم وسريع. المكتبة الكاملة تقدم لك محرك بحث قوي في التراث الإسلامي">
        <meta name="keywords" content="بحث في الكتب الإسلامية, كتب إسلامية, مكتبة إسلامية, التراث الإسلامي, بحث متقدم, كتب عربية">
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>البحث الفوري المُحسَّن - {{ config('app.name') }}</title>
        
        {{-- Vite Assets (Tailwind CSS + JS) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
        <style>
            .font-tajawal {
                font-family: 'Tajawal', sans-serif;
            }
            
            /* RTL Search Enhancements */
            .search-container {
                direction: rtl;
                text-align: right;
            }
            
            .search-input {
                text-align: right;
                direction: rtl;
            }
            
            /* Loading spinner */
            .spinner {
                border: 3px solid #f3f3f3;
                border-top: 3px solid #10b981;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            /* Search result highlights */
            mark {
                background-color: #fef3c7;
                padding: 0 2px;
                border-radius: 2px;
                font-weight: 600;
            }
            
            /* Search filters */
            .filter-select {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 9 4 4 4-4'/%3e%3c/svg%3e");
                background-position: left 0.5rem center;
                background-repeat: no-repeat;
                background-size: 1.5em 1.5em;
                padding-left: 2.5rem;
            }
            
            /* Result item hover effects */
            .result-item:hover {
                background-color: #f9fafb;
                border-color: #10b981;
            }
            
            /* Responsive design for mobile */
            @media (max-width: 768px) {
                .search-filters {
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .search-input {
                    font-size: 16px; /* Prevent zoom on iOS */
                }
            }
        </style>
    </head>
    <body class="bg-gray-50">
    <div class="page-wrapper relative z-[1] search-container font-tajawal" dir="rtl">
        <main class="relative overflow-hidden main-wrapper bg-white">
            <div class="relative">
                <div class="pattern-top top-24"></div>
                
                <!-- Search Interface -->
                <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" style="padding-top: 8rem;">
                    
                    <!-- Search Box -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        
                        <div class="space-y-4">
                            <!-- Main Search Input with Integrated Options -->
                            <div class="relative">
                                <!-- شريط البحث مع الأيقونات المدمجة -->
                                <div class="relative flex items-center bg-gray-50 rounded-lg border border-gray-300 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-200">
                                    <!-- أيقونة البحث في البداية -->
                                    <div class="flex items-center pointer-events-none px-4 border-l border-gray-300">
                                        <svg id="searchIcon" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <div id="searchSpinner" class="hidden w-5 h-5 text-emerald-500">
                                            <svg class="animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- حقل البحث -->
                                    <input 
                                        type="text"
                                        id="instantSearch" 
                                        placeholder="ابحث في المكتبة الكاملة... (من أول حرف)"
                                        value="{{ request('q', '') }}"
                                        class="w-full px-4 py-4 bg-transparent border-0 focus:outline-none text-lg"
                                        dir="rtl"
                                        autocomplete="off"
                                    />
                                    
                                    <!-- أيقونات خيارات البحث -->
                                    <div class="flex items-center gap-2 px-4 border-r border-gray-300">
                                        <!-- أيقونة الإعدادات -->
                                        <div class="relative">
                                            <button 
                                                type="button" 
                                                id="settingsToggle"
                                                class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors"
                                                title="إعدادات البحث"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span>إعدادات</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- قائمة الإعدادات -->
                                            <div id="settingsDropdown" class="hidden absolute top-full left-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-30 max-h-80 overflow-y-auto">
                                                <div class="p-3">
                                                    <!-- طبيعة البحث -->
                                                    <div class="mb-4">
                                                        <h3 class="text-sm font-medium text-gray-700 mb-3 text-right">نوع البحث</h3>
                                                        <div class="space-y-2">
                                                            <label class="flex items-center gap-3 p-3 hover:bg-emerald-50 rounded-lg cursor-pointer border border-gray-200 hover:border-emerald-500 transition-all search-type-label" title="بحث مرن يتعامل مع الكلمات بشكل ذكي مع تطبيع عربي" style="line-height: 1.35rem;">
                                                                <input type="radio" name="searchType" value="flexible_match" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-semibold block text-gray-800">البحث الغير مطابق</span>
                                                                        <span class="text-xs text-gray-500">يتجاهل (ال، و، ف)والهمزات وعلامات الترقيم </span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <label class="flex items-center gap-3 p-3 hover:bg-green-50 rounded-lg cursor-pointer border border-gray-200 hover:border-green-800 transition-all search-type-label" title="مطابقة حرفية دقيقة للنص المدخل" style="line-height: 1.35rem;">
                                                                <input type="radio" name="searchType" value="exact_match" class="text-green-800 focus:ring-green-800 w-4 h-4" checked>
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-semibold block text-gray-800">البحث المطابق</span>
                                                                        <span class="text-xs text-gray-500">مطابقة حرفية دقيقة للنص</span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <label class="flex items-center gap-3 p-3 hover:bg-purple-50 rounded-lg cursor-pointer border border-gray-200 hover:border-purple-500 transition-all search-type-label" title="بحث صرفي يتعامل مع الجذور والمشتقات" style="line-height: 1.35rem;">
                                                                <input type="radio" name="searchType" value="morphological" class="text-purple-600 focus:ring-purple-500 w-4 h-4">
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-semibold block text-gray-800">البحث الصرفي</span>
                                                                        <span class="text-xs text-gray-500">جذور ومشتقات</span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            
                                                            <!-- Disabled Options -->
                                                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-300 opacity-50 cursor-not-allowed relative" title="غير متوفر حالياً" style="line-height: 1.35rem;">
                                                                <input type="radio" name="searchType" value="fuzzy" disabled class="text-gray-400 w-4 h-4 cursor-not-allowed">
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-semibold block text-gray-500">تصحيح الأخطاء</span>
                                                                        <span class="text-xs text-gray-400">غير متوفر حالياً</span>
                                                                    </div>
                                                                    <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">قريباً</span>
                                                                </div>
                                                            </label>
                                                            
                                                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-300 opacity-50 cursor-not-allowed relative" title="غير متوفر حالياً">
                                                                <input type="radio" name="searchType" value="prefix" disabled class="text-gray-400 w-4 h-4 cursor-not-allowed">
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-semibold block text-gray-500">البحث بالبداية</span>
                                                                        <span class="text-xs text-gray-400">غير متوفر حالياً</span>
                                                                    </div>
                                                                    <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">قريباً</span>
                                                                </div>
                                                            </label>
                                                            
                                                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-300 opacity-50 cursor-not-allowed relative" title="غير متوفر حالياً">
                                                                <input type="radio" name="searchType" value="wildcard" disabled class="text-gray-400 w-4 h-4 cursor-not-allowed">
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-semibold block text-gray-500">الرموز البديلة</span>
                                                                        <span class="text-xs text-gray-400">غير متوفر حالياً</span>
                                                                    </div>
                                                                    <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">قريباً</span>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <!-- مطابقة الكلمات -->
                                                    <div class="mb-4 border-t pt-4">
                                                        <h3 class="text-sm font-medium text-gray-700 mb-3 text-right">مطابقة الكلمات</h3>
                                                        <div class="space-y-2">
                                                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-all" title="يكفي وجود أي كلمة من كلمات البحث">
                                                                <input type="radio" name="wordMatch" value="some_words" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-medium text-gray-800">بعض الكلمات</span>
                                                                        <span class="text-xs text-gray-500 block">يكفي وجود أي كلمة من كلمات البحث (OR)</span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-all" title="يجب أن توجد كل كلمات البحث">
                                                                <input type="radio" name="wordMatch" value="all_words" class="text-blue-600 focus:ring-blue-500 w-4 h-4" checked>
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-medium text-gray-800">جميع الكلمات</span>
                                                                        <span class="text-xs text-gray-500 block">يجب أن توجد كل كلمات البحث (AND)</span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <!-- ترتيب الكلمات في البحث -->
                                                    <div class="mb-4 border-t pt-4">
                                                        <h3 class="text-sm font-medium text-gray-700 mb-3 text-right">ترتيب الكلمات</h3>
                                                        <div class="space-y-2">
                                                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-all" title="الكلمات متتالية بدون فاصل">
                                                                <input type="radio" name="wordOrder" value="consecutive" class="text-green-800 focus:ring-green-800 w-4 h-4" checked>
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-medium text-gray-800">متتالية</span>
                                                                        <span class="text-xs text-gray-500 block">بدون كلمات بينها</span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-all" title="الكلمات في نفس الفقرة">
                                                                <input type="radio" name="wordOrder" value="same_paragraph" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-medium text-gray-800">نفس الفقرة</span>
                                                                        <span class="text-xs text-gray-500 block">مع كلمات بينها</span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-all" title="الكلمات في أي مكان من الصفحة">
                                                                <input type="radio" name="wordOrder" value="any_order" class="text-gray-600 focus:ring-gray-500 w-4 h-4">
                                                                <div class="flex items-center gap-2 flex-1 text-right">
                                                                    <div class="flex-1">
                                                                        <span class="text-sm font-medium text-gray-800">أي ترتيب</span>
                                                                        <span class="text-xs text-gray-500 block">في أي مكان</span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- أيقونة ترتيب النتائج -->
                                        <div class="relative">
                                            <button 
                                                type="button" 
                                                id="sortToggle"
                                                class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors"
                                                title="ترتيب النتائج"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                                </svg>
                                                <span id="sortLabel">أقرب صلة</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- قائمة ترتيب النتائج -->
                                            <div id="sortDropdown" class="hidden absolute top-full left-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-30">
                                                <div class="p-2">
                                                    <div class="space-y-1">
                                                        <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-md cursor-pointer">
                                                            <input type="radio" name="sortOrder" value="relevance" class="text-emerald-600 focus:ring-emerald-500" checked>
                                                            <div class="flex items-center gap-2 flex-1 text-right">
                                                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                                </svg>
                                                                <span class="text-sm">أقرب صلة</span>
                                                            </div>
                                                        </label>
                                                        <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-md cursor-pointer">
                                                            <input type="radio" name="sortOrder" value="death_year_asc" class="text-green-800 focus:ring-green-800">
                                                            <div class="flex items-center gap-2 flex-1 text-right">
                                                                <svg class="w-4 h-4 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                                <span class="text-sm">سنة الوفاة (الأقدم أولاً)</span>
                                                            </div>
                                                        </label>
                                                        <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-md cursor-pointer">
                                                            <input type="radio" name="sortOrder" value="death_year_desc" class="text-purple-600 focus:ring-purple-500">
                                                            <div class="flex items-center gap-2 flex-1 text-right">
                                                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                                <span class="text-sm">سنة الوفاة (الأحدث أولاً)</span>
                                                            </div>
                                                        </label>
                                                        <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-md cursor-pointer">
                                                            <input type="radio" name="sortOrder" value="book_title" class="text-orange-600 focus:ring-orange-500">
                                                            <div class="flex items-center gap-2 flex-1 text-right">
                                                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                                </svg>
                                                                <span class="text-sm">اسم الكتاب (أبجدياً)</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- أيقونة الفلترة -->
                                        <div class="relative">
                                            <button 
                                                type="button" 
                                                id="filterToggle"
                                                class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 hover:text-green-800 hover:bg-green-50 rounded-md transition-all relative"
                                                title="خيارات الفلترة"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                                </svg>
                                                <span id="filterToggleLabel" class="font-medium">فلترة</span>
                                                <!-- عداد الفلاتر النشطة - محسّن -->
                                                <span id="activeFiltersCount" class="hidden absolute -top-2 -right-2 bg-gradient-to-r from-red-900 text-white text-xs rounded-full min-w-[22px] h-[22px] px-1.5 flex items-center justify-center font-bold shadow-lg border-2 border-white animate-bounce">0</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- قائمة الفلترة -->
                                            <div id="filterDropdown" class="hidden absolute top-full left-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 z-30">
                                                <div class="p-4">
                                                    <div class="space-y-4">
                                                        <!-- فلترة حسب القسم -->
                                                        <div>
                                                            <button type="button" class="filter-category-btn w-full flex items-center justify-between p-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md" data-filter="section">
                                                                <span>القسم</span>
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- فلترة حسب الكتاب -->
                                                        <div>
                                                            <button type="button" class="filter-category-btn w-full flex items-center justify-between p-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md" data-filter="book">
                                                                <span>الكتاب</span>
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- فلترة حسب المؤلف 
                                                        <div style="display: none;">
                                                            <button type="button" class="filter-category-btn w-full flex items-center justify-between p-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md" data-filter="author">
                                                                <span>المؤلف</span>
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        -->
                                                        <!-- فلترة حسب تاريخ الوفاة 
                                                        <div style="display: none;">
                                                            <button type="button" class="filter-category-btn w-full flex items-center justify-between p-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md" data-filter="death_date">
                                                                <span>تاريخ الوفاة</span>
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <!-- أيقونة المساعدة -->
                                        <button 
                                            type="button" 
                                            onclick="showHelpModal()"
                                            class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors"
                                            title="مساعدة"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- الحقول المخفية للقيم -->
                                <select id="perPageSelect" class="hidden">
                                    <option value="10">10 نتائج</option>
                                    <option value="15" selected>15 نتيجة</option>
                                    <option value="25">25 نتيجة</option>
                                    <option value="50">50 نتيجة</option>
                                </select>
                            </div>

                            <!-- منطقة عرض Tags المختارة -->
                            <!-- الفلاتر المختارة - منطقة محسنة بصرياً -->
                            <div id="selectedFiltersContainer" class="hidden mt-4 p-4 bg-gradient-to-r from-green-50 via-emerald-50 to-green-100 rounded-xl border-2 border-green-800 shadow-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-green-800 rounded-full p-1.5">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-bold text-green-900">الفلاتر المطبقة:</span>
                                        <span id="filterSummaryText" class="text-xs font-semibold text-green-700 bg-white px-3 py-1 rounded-full shadow-sm border border-green-200"></span>
                                    </div>
                                    <button type="button" id="clearAllFilters" class="flex items-center gap-1 text-xs font-bold text-white bg-gradient-to-r from-red-900  hover:from-red-600  px-4 py-2 rounded-full transition-all shadow-md  transform hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        مسح الكل
                                    </button>
                                </div>
                                <div id="selectedFiltersTags" class="flex flex-wrap gap-2">
                                    <!-- Tags will be dynamically added here -->
                                </div>
                            </div>

                            <!-- شرح مبسط لأنواع البحث 
                            <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 border-r-4 border-green-800 text-sm text-gray-700 rounded-lg shadow-sm">
                                <div class="font-bold text-green-900 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>شرح أنواع البحث</span>
                                </div>
                                <div class="space-y-2 text-right">
                                    <div class="flex items-start gap-2">
                                        <span class="text-lg flex-shrink-0">🔄</span>
                                        <div>
                                            <strong class="text-emerald-700">البحث المرن:</strong>
                                            <span class="text-gray-600">بحث ذكي مع معالجة النصوص العربية (التاء المربوطة، الألف، الهمزات)</span>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="text-lg flex-shrink-0">🎯</span>
                                        <div>
                                            <strong class="text-green-800">البحث المطابق:</strong>
                                            <span class="text-gray-600">مطابقة حرفية دقيقة للنص كما كتبته بالضبط</span>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="text-lg flex-shrink-0">🌳</span>
                                        <div>
                                            <strong class="text-purple-700">البحث الصرفي:</strong>
                                            <span class="text-gray-600">بحث في الجذور والمشتقات (مثال: صلى → صلاة، صلوات، يصلي)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            -->
                            <!-- Search Stats -->
                            <div id="searchInfo" class="text-sm text-gray-600 hidden">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span id="resultCount"></span>
                                        <span id="searchTime">(خلال <span></span> ميلي ثانية)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500">عدد النتائج في الصفحة:</span>
                                        <div class="relative">
                                            <button 
                                                type="button" 
                                                id="perPageToggle"
                                                class="flex items-center gap-1 px-3 py-1 text-sm text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors border border-gray-300"
                                                title="عدد النتائج في الصفحة"
                                            >
                                                <span id="perPageLabel">15</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- قائمة عدد النتائج -->
                                            <div id="perPageDropdown" class="hidden absolute top-full left-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 z-30">
                                                <div class="p-2">
                                                    <div class="space-y-1">
                                                        <button type="button" data-per-page="10" class="per-page-option w-full text-center px-3 py-2 text-sm hover:bg-gray-50 rounded-md">10</button>
                                                        <button type="button" data-per-page="15" class="per-page-option w-full text-center px-3 py-2 text-sm hover:bg-gray-50 rounded-md">15</button>
                                                        <button type="button" data-per-page="25" class="per-page-option w-full text-center px-3 py-2 text-sm hover:bg-gray-50 rounded-md">25</button>
                                                        <button type="button" data-per-page="50" class="per-page-option w-full text-center px-3 py-2 text-sm hover:bg-gray-50 rounded-md">50</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div class="space-y-4">
                        <!-- Loading State -->
                        <div id="searchLoading" class="text-center py-8 hidden">
                            <div class="spinner mx-auto mb-4"></div>
                            <p class="text-gray-600">جاري البحث...</p>
                        </div>

                        <!-- Welcome State -->
                        <div id="welcomeMessage" class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">ابدأ البحث الآن</h3>
                            <p class="text-gray-500 mb-4">ابدأ الكتابة لرؤية النتائج فوراً</p>
                        </div>

                        <!-- No Results -->
                        <div id="noResults" class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200 hidden">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.467-.881-6.08-2.33"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد نتائج</h3>
                            <p class="text-gray-600">جرب استخدام كلمات مختلفة أو قم بتعديل المرشحات</p>
                        </div>

                        <!-- Error State -->
                        <div id="searchError" class="text-center py-12 bg-white rounded-lg shadow-sm border border-red-200 hidden">
                            <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-red-700 mb-2">خطأ في البحث</h3>
                            <p class="text-red-500">حدث خطأ أثناء البحث، يرجى المحاولة مرة أخرى</p>
                        </div>

                        <!-- Results List -->
                        <div id="searchResults" class="space-y-4 hidden">
                            <!-- Results will be inserted here -->
                        </div>

                        <!-- Pagination -->
                        <div id="paginationContainer" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hidden">
                            <!-- Pagination will be inserted here -->
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <style>
        .highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 500;
        }

        /* Selected result (keyboard navigation) */
        .result-selected {
            outline: 3px solid rgba(16,185,129,0.15);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.08);
        }
        
        .result-card {
            transition: box-shadow 0.2s ease;
        }
        
        .result-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .modal-overlay {
            backdrop-filter: blur(3px);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .more-options-menu {
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toast {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
    </style>

    <script>
        // البحث الفوري المُحسَّن
        class UltraFastSearch {
            constructor() {
                this.searchInput = document.getElementById('instantSearch');
                this.searchSpinner = document.getElementById('searchSpinner');
                this.searchIcon = document.getElementById('searchIcon');
                this.searchInfo = document.getElementById('searchInfo');
                this.searchTime = document.getElementById('searchTime');
                this.resultCount = document.getElementById('resultCount');
                this.resultsContainer = document.getElementById('searchResults');
                this.welcomeMessage = document.getElementById('welcomeMessage');
                this.noResults = document.getElementById('noResults');
                this.searchError = document.getElementById('searchError');
                this.perPageSelect = document.getElementById('perPageSelect');
                
                this.searchTimeout = null;
                this.currentPage = 1;
                this.currentFilterType = ''; // Fix: Added as class property for filter scope
                
                this.init();
            }
            
            init() {
                // البحث أثناء الكتابة
                this.searchInput.addEventListener('input', (e) => {
                    clearTimeout(this.searchTimeout);
                    const query = e.target.value.trim();
                    
                    if (query.length === 0) {
                        this.showWelcome();
                        return;
                    }
                    
                    if (query.length >= 1) { // بحث من أول حرف
                        this.searchTimeout = setTimeout(() => {
                            this.performSearch(query);
                        }, 300); // تأخير 300ms للسرعة
                    }
                });
                
                // تغيير عدد النتائج
                this.perPageSelect.addEventListener('change', () => {
                    const query = this.searchInput.value.trim();
                    if (query.length >= 1) {
                        this.performSearch(query);
                    }
                });
                
                // إعداد الأيقونات والقوائم المنسدلة الجديدة
                this.initIconDropdowns();
                
                // تغيير تباعد الكلمات
            }
            
            initIconDropdowns() {
                // إعداد قائمة الإعدادات الجديدة
                const settingsToggle = document.getElementById('settingsToggle');
                const settingsDropdown = document.getElementById('settingsDropdown');
                
                if (settingsToggle && settingsDropdown) {
                    settingsToggle.addEventListener('click', (e) => {
                        e.stopPropagation();
                        settingsDropdown.classList.toggle('hidden');
                        // إغلاق القوائم الأخرى
                        const sortDropdown = document.getElementById('sortDropdown');
                        const filterDropdown = document.getElementById('filterDropdown');
                        if (sortDropdown) sortDropdown.classList.add('hidden');
                        if (filterDropdown) filterDropdown.classList.add('hidden');
                    });
                    
                    // إعداد خيارات نوع البحث (النظام الجديد)
                    const searchTypeInputs = document.querySelectorAll('input[name="searchType"]');
                    searchTypeInputs.forEach(input => {
                        input.addEventListener('change', () => {
                            // إعادة البحث
                            const query = this.searchInput.value.trim();
                            if (query.length >= 1) {
                                this.performSearch(query);
                            }
                        });
                    });
                    
                    // إعداد خيارات ترتيب الكلمات
                    const wordOrderInputs = document.querySelectorAll('input[name="wordOrder"]');
                    wordOrderInputs.forEach(input => {
                        input.addEventListener('change', () => {
                            // إعادة البحث عند تغيير الترتيب
                            const query = this.searchInput.value.trim();
                            if (query.length >= 1) {
                                this.performSearch(query);
                            }
                        });
                    });
                    
                    // تحسين التفاعل البصري - Highlight للعنصر المحدد
                    const updateSearchTypeHighlight = () => {
                        document.querySelectorAll('.search-type-label').forEach(label => {
                            label.classList.remove('bg-emerald-50', 'border-emerald-500', 'bg-green-50', 'border-green-800', 'bg-purple-50', 'border-purple-500');
                        });
                        
                        const checked = document.querySelector('input[name="searchType"]:checked');
                        if (checked) {
                            const label = checked.closest('label');
                            const value = checked.value;
                            if (value === 'flexible_match') {
                                label.classList.add('bg-emerald-50', 'border-emerald-500');
                            } else if (value === 'exact_match') {
                                label.classList.add('bg-green-50', 'border-green-800');
                            } else if (value === 'morphological') {
                                label.classList.add('bg-purple-50', 'border-purple-500');
                            }
                        }
                    };
                    
                    // تطبيق highlight عند البداية
                    updateSearchTypeHighlight();
                    
                    // تحديث عند التغيير
                    searchTypeInputs.forEach(input => {
                        input.addEventListener('change', updateSearchTypeHighlight);
                    });
                }
                
                // إغلاق القوائم عند النقر خارجها
                document.addEventListener('click', () => {
                    const settingsDropdown = document.getElementById('settingsDropdown');
                    const sortDropdown = document.getElementById('sortDropdown');
                    const filterDropdown = document.getElementById('filterDropdown');
                    
                    if (settingsDropdown) settingsDropdown.classList.add('hidden');
                    if (sortDropdown) sortDropdown.classList.add('hidden');
                    if (filterDropdown) filterDropdown.classList.add('hidden');
                });
                
                // إعداد قائمة عدد النتائج في قسم معلومات البحث
                this.setupSearchInfoPerPageDropdown();
                
                // إعداد القوائم المنسدلة الجديدة
                this.setupSortDropdown();
                this.setupFilterDropdown();
                this.setupClearAllFilters();
            }
            
            setupSearchInfoPerPageDropdown() {
                const perPageToggleInfo = document.getElementById('perPageToggle');
                const perPageDropdownInfo = document.getElementById('perPageDropdown');
                const perPageLabelInfo = document.getElementById('perPageLabel');
                const perPageOptionsInfo = document.querySelectorAll('.per-page-option');
                
                if (perPageToggleInfo && perPageDropdownInfo) {
                    perPageToggleInfo.addEventListener('click', (e) => {
                        e.stopPropagation();
                        perPageDropdownInfo.classList.toggle('hidden');
                    });
                    
                    perPageOptionsInfo.forEach(option => {
                        option.addEventListener('click', () => {
                            const perPage = option.dataset.perPage;
                            const text = option.textContent.trim();
                            
                            // تحديث النص المعروض
                            perPageLabelInfo.textContent = text;
                            
                            // تحديث القيمة المخفية
                            this.perPageSelect.value = perPage;
                            
                            // إغلاق القائمة
                            perPageDropdownInfo.classList.add('hidden');
                            
                            // إعادة البحث
                            const query = this.searchInput.value.trim();
                            if (query.length >= 1) {
                                this.performSearch(query);
                            }
                        });
                    });
                    
                    // إغلاق القائمة عند النقر خارجها
                    document.addEventListener('click', () => {
                        perPageDropdownInfo.classList.add('hidden');
                    });
                }
            }
            
            setupSortDropdown() {
                const sortToggle = document.getElementById('sortToggle');
                const sortDropdown = document.getElementById('sortDropdown');
                const sortLabel = document.getElementById('sortLabel');
                const sortOptions = document.querySelectorAll('input[name="sortOrder"]');
                
                if (sortToggle && sortDropdown) {
                    sortToggle.addEventListener('click', (e) => {
                        e.stopPropagation();
                        sortDropdown.classList.toggle('hidden');
                    });
                    
                    sortOptions.forEach(option => {
                        option.addEventListener('change', () => {
                            const value = option.value;
                            let text = '';
                            
                            switch(value) {
                                case 'relevance':
                                    text = 'أقرب صلة';
                                    break;
                                case 'death_year_asc':
                                    text = 'سنة الوفاة (الأقدم أولاً)';
                                    break;
                                case 'death_year_desc':
                                    text = 'سنة الوفاة (الأحدث أولاً)';
                                    break;
                                case 'book_title':
                                    text = 'اسم الكتاب (أبجدياً)';
                                    break;
                            }
                            
                            sortLabel.textContent = text;
                            sortDropdown.classList.add('hidden');
                            
                            // إعادة البحث مع الترتيب الجديد
                            const query = this.searchInput.value.trim();
                            if (query.length >= 1) {
                                this.performSearch(query);
                            }
                        });
                    });
                    
                    // إغلاق القائمة عند النقر خارجها
                    document.addEventListener('click', () => {
                        sortDropdown.classList.add('hidden');
                    });
                }
            }
            
            setupFilterDropdown() {
                const filterToggle = document.getElementById('filterToggle');
                const filterDropdown = document.getElementById('filterDropdown');
                const filterCategoryBtns = document.querySelectorAll('.filter-category-btn');
                const filterModal = document.getElementById('filterModal');
                const filterModalTitle = document.getElementById('filterModalTitle');
                const closeFilterModal = document.getElementById('closeFilterModal');
                const cancelFilterModal = document.getElementById('cancelFilterModal');
                const applyFilterModal = document.getElementById('applyFilterModal');
                const clearFilterSelection = document.getElementById('clearFilterSelection');
                const filterSearch = document.getElementById('filterSearch');
                const filterOptionsList = document.getElementById('filterOptionsList');
                const dateRangeContainer = document.getElementById('dateRangeContainer');
                const filterSearchContainer = document.getElementById('filterSearchContainer');
                
                // Removed: let currentFilterType = ''; (now using this.currentFilterType)
                let selectedFilters = {
                    section: [],
                    book: [],
                    author: [],
                    death_date: { from: '', to: '' }
                };
                
                if (filterToggle && filterDropdown) {
                    filterToggle.addEventListener('click', (e) => {
                        e.stopPropagation();
                        filterDropdown.classList.toggle('hidden');
                    });
                    
                    filterCategoryBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            this.currentFilterType = btn.dataset.filter;
                            filterDropdown.classList.add('hidden');
                            
                            // تحديث عنوان المودال
                            const titles = {
                                section: 'فلترة حسب القسم',
                                book: 'فلترة حسب الكتاب',
                                author: 'فلترة حسب المؤلف',
                                death_date: 'فلترة حسب تاريخ الوفاة'
                            };
                            filterModalTitle.textContent = titles[this.currentFilterType];
                            
                            // إظهار/إخفاء عناصر حسب نوع الفلتر
                            if (this.currentFilterType === 'death_date') {
                                dateRangeContainer.classList.remove('hidden');
                                filterSearchContainer.classList.add('hidden');
                                filterOptionsList.classList.add('hidden');
                            } else {
                                dateRangeContainer.classList.add('hidden');
                                filterSearchContainer.classList.remove('hidden');
                                filterOptionsList.classList.remove('hidden');
                                window.ultraFastSearch.loadFilterOptions(this.currentFilterType);
                            }
                            
                            filterModal.classList.remove('hidden');
                        });
                    });
                    
                    // إغلاق المودال
                    [closeFilterModal, cancelFilterModal].forEach(btn => {
                        btn.addEventListener('click', () => {
                            filterModal.classList.add('hidden');
                        });
                    });
                    
                    // تطبيق الفلتر - Context7 Enhanced with debugging
                    applyFilterModal.addEventListener('click', (e) => {
                        console.log('Apply filter button clicked!', {
                            currentFilterType: this.currentFilterType,
                            selectedCheckboxes: document.querySelectorAll('.filter-option-checkbox:checked').length
                        });
                        
                        try {
                            window.ultraFastSearch.applyCurrentFilter();
                            filterModal.classList.add('hidden');
                            console.log('Filter applied successfully');
                        } catch (error) {
                            console.error('Error applying filter:', error);
                        }
                    });
                    
                    // مسح التحديد
                    clearFilterSelection.addEventListener('click', () => {
                        window.ultraFastSearch.clearCurrentFilterSelection();
                    });
                    
                    // البحث في الخيارات
                    filterSearch.addEventListener('input', (e) => {
                        window.ultraFastSearch.filterOptionsSearch(e.target.value);
                    });
                    
                    // إغلاق القائمة عند النقر خارجها
                    document.addEventListener('click', () => {
                        filterDropdown.classList.add('hidden');
                    });
                    
                    // إغلاق المودال عند النقر على الخلفية
                    filterModal.addEventListener('click', (e) => {
                        if (e.target === filterModal) {
                            filterModal.classList.add('hidden');
                        }
                    });
                }
                
                this.selectedFilters = selectedFilters;
            }
            
            async loadFilterOptions(filterType) {
                const filterOptionsList = document.getElementById('filterOptionsList');
                
                // عرض مؤشر التحميل - Context7 Enhanced
                filterOptionsList.innerHTML = `
                    <div class="flex items-center justify-center p-6">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-800"></div>
                        <span class="mr-3 text-sm text-gray-600">جاري تحميل البيانات الحقيقية...</span>
                    </div>
                `;
                
                try {
                    // Context7: Use new endpoint for real data
                    const endpoint = filterType === 'book' ? 'books' : 
                                   filterType === 'section' ? 'sections' : filterType;
                    
                    const response = await fetch(`/api/available-filters?type=${endpoint}&limit=100`);
                    const result = await response.json();
                    
                    if (result.success && result.data) {
                        const filterData = result.data[endpoint] || result.data;
                        
                        if (filterData.length === 0) {
                            filterOptionsList.innerHTML = `
                                <div class="p-4 text-center text-gray-500">
                                    <div class="mb-2">📭</div>
                                    <div class="text-sm">لا توجد خيارات متاحة</div>
                                </div>
                            `;
                            return;
                        }
                        
                        // Context7: Enhanced display with counts
                        filterOptionsList.innerHTML = filterData.map(option => `
                            <label class="flex items-center justify-between gap-3 p-3 hover:bg-green-50 rounded-lg cursor-pointer border border-transparent hover:border-green-800 transition-all">
                                <div class="flex items-center gap-3 flex-1">
                                    <input type="checkbox" value="${option.id}" data-name="${option.name}" 
                                           class="text-green-800 focus:ring-green-800 filter-option-checkbox rounded">
                                    <div class="flex-1 text-right">
                                        <div class="text-sm font-medium text-gray-900">${option.name}</div>
                                    </div>
                                </div>
                            </label>
                        `).join('');
                        
                        // تحديد الخيارات المختارة مسبقاً
                        const selectedOptions = this.selectedFilters[filterType] || [];
                        filterOptionsList.querySelectorAll('.filter-option-checkbox').forEach(checkbox => {
                            if (selectedOptions.includes(checkbox.value)) {
                                checkbox.checked = true;
                            }
                        });
                    } else {
                        throw new Error(result.message || 'فشل في جلب البيانات');
                    }
                } catch (error) {
                    console.error('Error loading filter options:', error);
                    
                    // Context7: Enhanced error handling with retry
                    filterOptionsList.innerHTML = `
                        <div class="text-center p-6 bg-red-50 border border-red-200 rounded-lg">
                            <div class="text-red-600 mb-3">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm font-medium">فشل في تحميل خيارات الفلاتر</p>
                                <p class="text-xs mt-1 text-red-500">${error.message}</p>
                            </div>
                            <button onclick="window.ultraFastSearch.loadFilterOptions('${filterType}')" 
                                    class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                                🔄 إعادة المحاولة
                            </button>
                        </div>
                    `;
                }
            }
            
            filterOptionsSearch(searchTerm) {
                const filterOptionsList = document.getElementById('filterOptionsList');
                const labels = filterOptionsList.querySelectorAll('label');
                
                labels.forEach(label => {
                    const text = label.textContent.toLowerCase();
                    const matches = text.includes(searchTerm.toLowerCase());
                    label.style.display = matches ? 'flex' : 'none';
                });
            }
            
            applyCurrentFilter() {
                console.log('applyCurrentFilter called', {
                    currentFilterType: this.currentFilterType,
                    selectedFilters: this.selectedFilters
                });
                
                if (this.currentFilterType === 'death_date') {
                    const from = document.getElementById('deathYearFrom').value;
                    const to = document.getElementById('deathYearTo').value;
                    this.selectedFilters.death_date = { from, to };
                    
                    if (from || to) {
                        this.addFilterTag('death_date', `${from || '...'} - ${to || '...'}`, { from, to });
                    }
                } else {
                    const checkboxes = document.querySelectorAll('.filter-option-checkbox:checked');
                    const selected = Array.from(checkboxes).map(cb => cb.value);
                    
                    console.log('Processing checkboxes', {
                        filterType: this.currentFilterType,
                        checkboxCount: checkboxes.length,
                        selectedValues: selected
                    });
                    
                    this.selectedFilters[this.currentFilterType] = selected;
                    
                    checkboxes.forEach(checkbox => {
                        const id = checkbox.value;
                        const name = checkbox.getAttribute('data-name');
                        console.log('Adding filter tag', { id, name, type: this.currentFilterType });
                        this.addFilterTag(this.currentFilterType, name, id);
                    });
                }
                
                this.updateFiltersDisplay();
                
                // Context7 Fix: إعادة البحث مع الفلاتر الجديدة - حتى بدون نص بحث
                const query = this.searchInput.value.trim();
                
                // البحث يجب أن يحدث إذا كان هناك استعلام أو فلاتر مطبقة
                const hasAppliedFilters = this.getAppliedFiltersCount() > 0;
                
                if (query.length >= 1 || hasAppliedFilters) {
                    console.log('Applying filters and performing search', {
                        query: query,
                        hasFilters: hasAppliedFilters,
                        filters: this.selectedFilters
                    });
                    this.performSearch(query);
                } else {
                    console.warn('No query or filters to apply');
                }
            }
            
            clearCurrentFilterSelection() {
                if (this.currentFilterType === 'death_date') {
                    document.getElementById('deathYearFrom').value = '';
                    document.getElementById('deathYearTo').value = '';
                } else {
                    document.querySelectorAll('.filter-option-checkbox').forEach(cb => {
                        cb.checked = false;
                    });
                }
            }
            
            addFilterTag(type, label, value) {
                const container = document.getElementById('selectedFiltersTags');
                const tagId = `tag-${type}-${Date.now()}`;
                
                const tag = document.createElement('div');
                tag.id = tagId;
                tag.className = 'inline-flex items-center gap-2 px-4 py-2 bg-green-900 text-white text-sm font-medium rounded-full shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200';
                tag.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>${label}</span>
                    <button type="button" class="bg-opacity-20 hover:bg-opacity-30 rounded-full p-1 transition-all" onclick="window.ultraFastSearch.removeFilterTag('${tagId}', '${type}', '${JSON.stringify(value).replace(/"/g, '&quot;')}')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                
                container.appendChild(tag);
                this.updateFiltersDisplay();
            }
            
            removeFilterTag(tagId, type, value) {
                const tag = document.getElementById(tagId);
                if (tag) {
                    tag.remove();
                    
                    // إزالة من البيانات المحفوظة
                    if (type === 'death_date') {
                        this.selectedFilters.death_date = { from: '', to: '' };
                    } else {
                        const parsedValue = JSON.parse(value.replace(/&quot;/g, '"'));
                        const index = this.selectedFilters[type].indexOf(parsedValue);
                        if (index > -1) {
                            this.selectedFilters[type].splice(index, 1);
                        }
                    }
                    
                    this.updateFiltersDisplay();
                    
                    // إعادة البحث
                    const query = this.searchInput.value.trim();
                    if (query.length >= 1) {
                        this.performSearch(query);
                    }
                }
            }
            
            updateFiltersDisplay() {
                const container = document.getElementById('selectedFiltersContainer');
                const tagsContainer = document.getElementById('selectedFiltersTags');
                const badge = document.getElementById('activeFiltersCount');
                const summaryText = document.getElementById('filterSummaryText');
                
                // حساب عدد الفلاتر المطبقة
                const filterCount = tagsContainer.children.length;
                
                if (filterCount > 0) {
                    container.classList.remove('hidden');
                    
                    // تحديث شارة العدد
                    if (badge) {
                        badge.textContent = filterCount;
                        badge.classList.remove('hidden');
                    }
                    
                    // تحديث نص الملخص
                    if (summaryText) {
                        summaryText.textContent = `${filterCount} ${filterCount === 1 ? 'فلتر' : 'فلاتر'} مطبقة`;
                    }
                } else {
                    container.classList.add('hidden');
                    
                    // إخفاء شارة العدد
                    if (badge) {
                        badge.classList.add('hidden');
                    }
                }
            }
            
            setupClearAllFilters() {
                const clearAllBtn = document.getElementById('clearAllFilters');
                const container = document.getElementById('selectedFiltersContainer');
                const tagsContainer = document.getElementById('selectedFiltersTags');
                const badge = document.getElementById('activeFiltersCount');
                
                if (clearAllBtn && !clearAllBtn.hasAttribute('data-listener-added')) {
                    clearAllBtn.addEventListener('click', () => {
                        tagsContainer.innerHTML = '';
                        this.selectedFilters = {
                            section: [],
                            book: [],
                            author: [],
                            death_date: { from: '', to: '' }
                        };
                        container.classList.add('hidden');
                        
                        // إخفاء شارة العدد
                        if (badge) {
                            badge.classList.add('hidden');
                        }
                        
                        // إعادة البحث
                        const query = this.searchInput.value.trim();
                        if (query.length >= 1) {
                            this.performSearch(query);
                        }
                    });
                    clearAllBtn.setAttribute('data-listener-added', 'true');
                }
            }
            
            showWelcome() {
                this.welcomeMessage.classList.remove('hidden');
                this.resultsContainer.classList.add('hidden');
                this.noResults.classList.add('hidden');
                this.searchError.classList.add('hidden');
                this.searchInfo.classList.add('hidden');
            }
            
            showLoading() {
                this.searchSpinner.classList.remove('hidden');
                this.searchIcon.classList.add('hidden');
            }
            
            hideLoading() {
                this.searchSpinner.classList.add('hidden');
                this.searchIcon.classList.remove('hidden');
            }
            
            async performSearch(query) {
                console.debug('UltraFastSearch.performSearch start', { query: query, page: this.currentPage });
                this.showLoading();
                
                const perPage = this.perPageSelect.value;
                const searchTypeInput = document.querySelector('input[name="searchType"]:checked');
                const searchType = searchTypeInput ? searchTypeInput.value : 'exact_match';
                const wordOrderInput = document.querySelector('input[name="wordOrder"]:checked');
                const wordOrder = wordOrderInput ? wordOrderInput.value : 'consecutive';
                const wordMatchInput = document.querySelector('input[name="wordMatch"]:checked');
                const wordMatch = wordMatchInput ? wordMatchInput.value : 'all_words';
                const startTime = performance.now();
                
                try {
                    const params = new URLSearchParams({
                        q: query,
                        per_page: perPage,
                        page: this.currentPage,
                        search_type: searchType,
                        word_order: wordOrder,
                        word_match: wordMatch,
                        });
                    
                    // إضافة الفلاتر المحددة (Context7: Add book_id support)
                    if (this.selectedFilters.author && this.selectedFilters.author.length > 0) {
                        params.append('author_id', this.selectedFilters.author.join(','));
                    }
                    if (this.selectedFilters.section && this.selectedFilters.section.length > 0) {
                        params.append('section_id', this.selectedFilters.section.join(','));
                    }
                    if (this.selectedFilters.book && this.selectedFilters.book.length > 0) {
                        params.append('book_id', this.selectedFilters.book.join(','));
                    }
                    
                    const response = await fetch(`/api/ultra-search?${params}`);
                    const data = await response.json();
                    console.debug('UltraFastSearch.performSearch response', data);
                    
                    const searchTime = Math.round(performance.now() - startTime);
                    
                    this.hideLoading();
                    
                    if (data.success && data.data && data.data.length > 0) {
                        console.debug('UltraFastSearch.performSearch will display results', data.data.length);
                        this.displayResults(data.data, data.pagination, searchTime, searchType, data.search_metadata);
                    } else {
                        // Context7: Enhanced no results with debugging info
                        console.warn('No results found:', {
                            query: query,
                            filters: this.selectedFilters,
                            response: data
                        });
                        
                        this.showNoResults(query);
                        
                        // Show debug info if there's metadata
                        if (data.search_metadata) {
                            console.info('Search metadata:', data.search_metadata);
                        }
                    }
                    
                } catch (error) {
                    console.error('Search error:', error);
                    this.hideLoading();
                    this.showError();
                }
            }
            
            displayResults(results, pagination, searchTime, searchType) {
                console.debug('UltraFastSearch.displayResults', { resultsCount: results.length, pagination: pagination });
                this.welcomeMessage.classList.add('hidden');
                this.noResults.classList.add('hidden');
                this.searchError.classList.add('hidden');
                this.resultsContainer.classList.remove('hidden');
                this.searchInfo.classList.remove('hidden');
                // تحديث معلومات البحث
                if (this.searchTime) this.searchTime.textContent = `${searchTime}ms`;
                if (this.resultCount) this.resultCount.textContent = `${pagination.total} نتيجة`;
                // عرض النتائج (تصميم متوافق RTL)
                // globalIndex helps maintain unique index across pages
                let globalStart = ((pagination.current_page - 1) * pagination.per_page) || 0;
                this.resultsContainer.innerHTML = results.map((result, index) => `
                    <div id="result-${globalStart + index}" data-page-id="${result.id}" tabindex="0" data-result-index="${globalStart + index}" class="result-item result-card bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-shrink-0 bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                                ${globalStart + index + 1}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    <a href="${result.url ? result.url : ('/book/' + result.book_id + '/' + result.page_number)}" class="hover:text-emerald-600 transition-colors">${result.book_title || 'كتاب غير محدد'}</a>
                                </h3>
                            </div>
                            <div class="text-right flex items-center gap-3">
                                <button onclick="toggleFullContent(${result.id})" class="toggle-btn toggle-btn-${result.id} px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded text-sm">🔍 عرض كاملة</button>
                                <div class="relative inline-block">
                                    <button onclick="toggleMoreOptions(${result.id})" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">⋯</button>
                                    <div id="more-options-${result.id}" class="hidden more-options-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-20 border border-gray-200">
                                        <div class="py-1">
                                            <button onclick="showRelatedPages(${result.book_id}, ${result.page_number})" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📄 صفحات مشابهة</button>
                                            <button onclick="copyContent(${result.id})" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📋 نسخ النص</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mr-12">
                            ${result.section ? `<div class="mb-2"><span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">${result.section}</span></div>` : ''}
                            <div class="text-sm text-gray-600 space-y-1 mb-3">
                                <div class="flex flex-wrap gap-4 items-center">
                                    ${result.section ? `<span><span class="font-medium">القسم:</span> ${result.section}</span>` : ''}
                                    ${result.chapter_title ? `<span><span class="font-medium">الفصل:</span> ${result.chapter_title}</span>` : ''}
                                    ${result.volume_title ? `<span><span class="font-medium">المجلد:</span> ${result.volume_title}</span>` : ''}
                                </div>
                            </div>
                        </div>

                        <div class="text-gray-700 leading-relaxed mb-3">
                            <div class="result-content-${result.id}">
                                <p class="mb-3">${result.highlight || result.content || result.content_preview || 'لا يوجد محتوى للعرض'}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center gap-6 text-sm text-gray-600">
                                ${result.author_name && result.author_name !== 'غير محدد' && result.author_name !== 'مؤلف' ? `<div class="flex items-center gap-2"><span class="font-medium text-gray-700">👤 المؤلف:</span> ${result.author_id ? `<a href="/authors/${result.author_id}/details" class="text-green-800 hover:text-green-900 hover:underline transition-colors">${result.author_name}</a>` : `<span class="text-gray-600">${result.author_name}</span>`}</div>` : result.author_name ? `<div class="flex items-center gap-2"><span class="font-medium text-gray-700"> المؤلف:</span> <span class="text-gray-600">غير محدد</span></div>` : ''}
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-700">📄 الصفحة:</span>
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">${result.page_number || 'غير محدد'}</span>
                                </div>
                            </div>

                        </div>

                        <div class="full-content-${result.id} hidden mt-4">
                            <!-- full content will load here -->
                        </div>
                    </div>
                `).join('');

                // re-init keyboard navigation
                initKeyboardNav();
                
                // إضافة شريط التنقل البسيط إذا كان هناك أكثر من صفحة واحدة
                if (pagination.last_page && pagination.last_page > 1) {
                    this.resultsContainer.innerHTML += `
                        <div id="paginationBar" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mt-6">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    صفحة <span class="font-medium">${pagination.current_page}</span> من <span class="font-medium">${pagination.last_page}</span>
                                </div>
                                <div class="flex space-x-2 space-x-reverse">
                                    <button onclick="window.searchInstance.goToPage(${Math.max(1, pagination.current_page - 1)})" ${pagination.current_page === 1 ? 'disabled' : ''} 
                                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors ${pagination.current_page === 1 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-emerald-600 text-white hover:bg-emerald-700'}">السابق</button>
                                    <button onclick="window.searchInstance.goToPage(${Math.min(pagination.last_page, pagination.current_page + 1)})" ${pagination.current_page === pagination.last_page ? 'disabled' : ''} 
                                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors ${pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-emerald-600 text-white hover:bg-emerald-700'}">التالي</button>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                // حفظ بيانات الصفحة الحالية
                this.currentPage = pagination.current_page;
                this.totalPages = pagination.last_page;
            }

            goToPage(page) {
                if (!page || page < 1) return;
                this.currentPage = page;
                const query = this.searchInput.value.trim();
                if (query.length >= 1) {
                    this.performSearch(query);
                }
            }

            generatePageNumbers(currentPage, totalPages) {
                if (totalPages <= 1) return '';
                
                let pages = [];
                const maxVisible = 5; // عدد الصفحات المرئية
                
                if (totalPages <= maxVisible) {
                    // إذا كان العدد الكلي أقل من أو يساوي الحد الأقصى، اعرض جميع الصفحات
                    for (let i = 1; i <= totalPages; i++) {
                        pages.push(i);
                    }
                } else {
                    // منطق ذكي لعرض الصفحات
                    if (currentPage <= 3) {
                        // في البداية: 1, 2, 3, 4, ..., last
                        pages = [1, 2, 3, 4];
                        if (totalPages > 5) pages.push('...');
                        pages.push(totalPages);
                    } else if (currentPage >= totalPages - 2) {
                        // في النهاية: 1, ..., last-3, last-2, last-1, last
                        pages = [1];
                        if (totalPages > 5) pages.push('...');
                        pages.push(totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
                    } else {
                        // في المنتصف: 1, ..., current-1, current, current+1, ..., last
                        pages = [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
                    }
                }
                
                return pages.map(page => {
                    if (page === '...') {
                        return '<span class="px-2 py-1 text-gray-400 text-sm">...</span>';
                    } else if (page === currentPage) {
                        return `<button class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium bg-emerald-600 text-white">${page}</button>`;
                    } else {
                        return `<button onclick="window.searchInstance.goToPage(${page})" class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-200">${page}</button>`;
                    }
                }).join('');
            }
            
            getSearchModeLabel(mode) {
                const labels = {
                    'flexible_match': 'مرن',
                    'exact_match': 'مطابق',
                    'morphological': 'صرفي'
                };
                return labels[mode] || 'مرن';
            }
            
            showNoResults(query = '') {
                this.welcomeMessage.classList.add('hidden');
                this.resultsContainer.classList.add('hidden');
                this.searchError.classList.add('hidden');
                this.noResults.classList.remove('hidden');
                this.searchInfo.classList.add('hidden');
                
                // Context7: Enhanced no results message with troubleshooting
                const appliedFilters = this.getAppliedFiltersCount();
                const filterSuggestion = appliedFilters > 0 ? 
                    `<div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center gap-2 text-yellow-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <span class="font-medium">اقتراح:</span>
                        </div>
                        <p class="text-sm text-yellow-700 mt-2">
                            لديك ${appliedFilters} فلتر مطبق. جرب إزالة بعض الفلاتر أو استخدم كلمات بحث أوسع.
                        </p>
                        <button onclick="window.ultraFastSearch.clearAllFilters()" 
                                class="mt-2 text-yellow-800 hover:text-yellow-900 underline text-sm">
                            🗑️ مسح جميع الفلاتر
                        </button>
                    </div>` : '';
                
                // Update no results content with suggestions
                const noResultsDiv = document.querySelector('.no-results');
                if (noResultsDiv) {
                    const originalContent = noResultsDiv.innerHTML;
                    if (!originalContent.includes('اقتراح:')) {
                        noResultsDiv.innerHTML = originalContent + filterSuggestion;
                    }
                }
            }
            
            getAppliedFiltersCount() {
                let count = 0;
                Object.values(this.selectedFilters).forEach(filter => {
                    if (Array.isArray(filter)) {
                        count += filter.length;
                    } else if (filter && typeof filter === 'object' && (filter.from || filter.to)) {
                        count += 1;
                    }
                });
                return count;
            }
            
            clearAllFilters() {
                const container = document.getElementById('selectedFiltersContainer');
                const tagsContainer = document.getElementById('selectedFiltersTags');
                const badge = document.getElementById('activeFiltersCount');
                
                if (tagsContainer) tagsContainer.innerHTML = '';
                this.selectedFilters = {
                    section: [],
                    book: [],
                    author: [],
                    death_date: { from: '', to: '' }
                };
                if (container) container.classList.add('hidden');
                if (badge) badge.classList.add('hidden');
                
                // إعادة البحث
                const query = this.searchInput.value.trim();
                if (query.length >= 1) {
                    this.performSearch(query);
                }
            }
            
            showError() {
                this.welcomeMessage.classList.add('hidden');
                this.resultsContainer.classList.add('hidden');
                this.noResults.classList.add('hidden');
                this.searchError.classList.remove('hidden');
                this.searchInfo.classList.add('hidden');
            }
        }
        
        // وظائف الانتقال والتفاعل مع النتائج
        function goToPage(bookId, pageNumber) {
            if (bookId && pageNumber) {
                window.location.href = `/book/${bookId}/${pageNumber}`;
            } else {
                alert('معلومات الصفحة غير متاحة');
            }
        }
        
        function goToBook(bookId) {
            if (bookId) {
                window.location.href = `/book/${bookId}`;
            } else {
                alert('معلومات الكتاب غير متاحة');
            }
        }
        
        // توسيع المحتوى للصفحة كاملة
        async function toggleFullContent(pageId) {
            // locate elements more robustly: by data-page-id wrapper, or by direct classes
            const wrapper = document.querySelector(`[data-page-id='${pageId}']`) || document.getElementById(`result-${pageId}`) || document.querySelector(`#result-${pageId}`);
            const toggleBtn = wrapper ? wrapper.querySelector(`.toggle-btn-${pageId}`) : document.querySelector(`.toggle-btn-${pageId}`);
            let shortContent = wrapper ? wrapper.querySelector(`.result-content-${pageId}`) : document.querySelector(`.result-content-${pageId}`);
            let fullContentDiv = wrapper ? wrapper.querySelector(`.full-content-${pageId}`) : document.querySelector(`.full-content-${pageId}`);

            // if fullContentDiv doesn't exist, create it at the end of wrapper
            if (!fullContentDiv && wrapper) {
                fullContentDiv = document.createElement('div');
                fullContentDiv.className = `full-content-${pageId} hidden mt-4`;
                wrapper.appendChild(fullContentDiv);
            }

            // if shortContent missing, try to find any child with 'result-content-' prefix
            if (!shortContent && wrapper) {
                shortContent = Array.from(wrapper.querySelectorAll('[class*="result-content-"]'))[0] || null;
            }

            if (!toggleBtn) {
                console.warn('toggleFullContent: button not found for pageId', pageId);
                return;
            }

            const isHidden = fullContentDiv ? fullContentDiv.classList.contains('hidden') : true;

            if (isHidden) {
                toggleBtn.textContent = '⏳ جاري التحميل...';
                toggleBtn.disabled = true;

                try {
                    const response = await fetch(`/api/page/${pageId}/full-content`);
                    const data = await response.json();


                        if (data && data.success) {
                        const currentQuery = document.getElementById('instantSearch').value || '';
                        // preserve original text and spacing, escape HTML to be safe
                        const raw = data.page.full_content || 'لا يوجد محتوى متاح';
                        const escaped = escapeHtml(raw);
                        const highlighted = currentQuery ? highlightTerms(escaped, currentQuery) : escaped;

                        fullContentDiv.innerHTML = `
                            <div class="text-gray-700 leading-relaxed mb-6" dir="rtl">
                                <div style="white-space: pre-wrap; word-wrap: break-word;">${highlighted}</div>
                            </div>
                            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-green-50 rounded-lg p-4 shadow-lg">
                                <button onclick="navigateToPage(${data.page.book_id}, ${data.page.page_number - 1})" 
                                        class="flex items-center gap-2 px-5 py-3 bg-white hover:bg-green-50 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200 hover:border-green-800 ${data.page.page_number <= 1 ? 'opacity-50' : ''}"
                                        ${data.page.page_number <= 1 ? 'disabled' : ''}>
                                    <span class="text-lg">→</span>
                                    <span>الصفحة السابقة</span>
                                </button>
                                <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-md border border-gray-200">
                                    <span class="text-green-800 font-bold">📄</span>
                                    <span class="text-sm font-bold text-gray-700">صفحة ${data.page.page_number}</span>
                                </div>
                                <button onclick="navigateToPage(${data.page.book_id}, ${data.page.page_number + 1})" 
                                        class="flex items-center gap-2 px-5 py-3 bg-white hover:bg-green-50 rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200 hover:border-green-800">
                                    <span>الصفحة التالية</span>
                                    <span class="text-lg">←</span>
                                </button>
                            </div>
                        `;

                        if (shortContent) shortContent.classList.add('hidden');
                        fullContentDiv.classList.remove('hidden');
                        toggleBtn.textContent = '📝 عرض مختصر';
                    } else {
                        alert('فشل في تحميل المحتوى الكامل');
                    }
                } catch (error) {
                    alert('خطأ في تحميل المحتوى');
                    console.error(error);
                }

                toggleBtn.disabled = false;
            } else {
                // العودة للعرض المختصر
                if (shortContent) shortContent.classList.remove('hidden');
                if (fullContentDiv) fullContentDiv.classList.add('hidden');
                toggleBtn.textContent = '🔍 عرض الصفحة كاملة';
            }
        }

        // escape HTML to display raw page content safely
        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // تضع علامة <mark> على مصطلحات البحث داخل النص (المحتوى مفترض أنه مُهَرب بالفعل)
        function highlightTerms(escapedText, query) {
            if (!escapedText || !query) return escapedText || '';
            try {
                const terms = query.split(/\s+/).filter(t => t.length > 0);
                if (terms.length === 0) return escapedText;

                // escape terms for regex
                const escaped = terms.map(t => t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
                const pattern = new RegExp('(' + escaped.join('|') + ')', 'gi');

                // Replace matches while preserving existing HTML entities
                return escapedText.replace(pattern, '<mark>$1</mark>');
            } catch (e) {
                return escapedText;
            }
        }

        // التنقل إلى صفحة معينة في الكتاب
        async function navigateToPage(bookId, pageNumber) {
            if (pageNumber < 1) return;
            
            try {
                const response = await fetch(`/api/book/${bookId}/page/${pageNumber}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: فشل في تحميل الصفحة`);
                }
                
                const data = await response.json();
                
                if (data && data.success && data.page) {
                    // البحث عن العنصر الحالي المفتوح للمحتوى الكامل
                    const currentFullContent = document.querySelector('[class*="full-content-"]:not(.hidden)');
                    
                    if (currentFullContent) {
                        // تحديث المحتوى الحالي بدلاً من فتح صفحة جديدة
                        const currentQuery = document.getElementById('instantSearch').value || '';
                        const raw = data.page.full_content || 'لا يوجد محتوى متاح';
                        const escaped = escapeHtml(raw);
                        const highlighted = currentQuery ? highlightTerms(escaped, currentQuery) : escaped;

                        currentFullContent.innerHTML = `
                            <div class="text-gray-700 leading-relaxed mb-6" dir="rtl">
                                <div style="white-space: pre-wrap; word-wrap: break-word;">${highlighted}</div>
                            </div>
                            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-green-50 rounded-lg p-4 shadow-lg">
                                <button onclick="navigateToPage(${data.page.book_id}, ${data.page.page_number - 1})" 
                                        class="flex items-center gap-2 px-5 py-3 bg-white hover:bg-green-50 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200 hover:border-green-800 ${data.page.page_number <= 1 ? 'opacity-50' : ''}"
                                        ${data.page.page_number <= 1 ? 'disabled' : ''}>
                                    <span class="text-lg">→</span>
                                    <span>الصفحة السابقة</span>
                                </button>
                                <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-md border border-gray-200">
                                    <span class="text-green-800 font-bold">📄</span>
                                    <span class="text-sm font-bold text-gray-700">صفحة ${data.page.page_number}</span>
                                </div>
                                <button onclick="navigateToPage(${data.page.book_id}, ${data.page.page_number + 1})" 
                                        class="flex items-center gap-2 px-5 py-3 bg-white hover:bg-green-50 rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200 hover:border-green-800">
                                    <span>الصفحة التالية</span>
                                    <span class="text-lg">←</span>
                                </button>
                            </div>
                        `;
                    } else {
                        // إذا لم يكن هناك محتوى كامل مفتوح، افتح المحتوى الكامل للصفحة الجديدة
                        await toggleFullContent(data.page.id);
                    }
                } else {
                    throw new Error(data.error || 'الصفحة غير موجودة');
                }
            } catch (error) {
                console.error('خطأ في التنقل:', error);
                alert('خطأ في تحميل الصفحة: ' + error.message);
            }
        }
        
        // تبديل خيارات "المزيد"
        function toggleMoreOptions(resultId) {
            const optionsDiv = document.getElementById(`more-options-${resultId}`);
            
            // إخفاء كل القوائم الأخرى
            document.querySelectorAll('[id^="more-options-"]').forEach(div => {
                if (div.id !== `more-options-${resultId}`) {
                    div.classList.add('hidden');
                }
            });
            
            // تبديل القائمة الحالية
            optionsDiv.classList.toggle('hidden');
        }
        
        // إظهار الصفحات المشابهة
        async function showRelatedPages(bookId, currentPage) {
            try {
                const response = await fetch(`/api/book/${bookId}/pages?per_page=10`);
                const data = await response.json();
                
                if (data.success && data.pages.length > 0) {
                    const pagesHtml = data.pages.map(page => `
                        <div class="border-b border-gray-200 pb-2 mb-2">
                            <button onclick="goToPage(${bookId}, ${page.page_number})" 
                                    class="text-green-800 hover:text-green-900 font-medium">
                                صفحة ${page.page_number}
                            </button>
                            <div class="text-sm text-gray-600 mt-1">${page.content_preview}</div>
                        </div>
                    `).join('');
                    
                    const modal = `
                        <div id="pages-modal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                            <div class="modal-content bg-white rounded-lg max-w-2xl w-full max-h-96 overflow-hidden shadow-lg">
                                <div class="bg-green-800 text-white p-6">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-bold">📚 صفحات من نفس الكتاب</h3>
                                        <button onclick="closeModal('pages-modal')" class="text-white hover:text-gray-200 text-xl font-bold">✕</button>
                                    </div>
                                    <p class="text-green-100 mt-2">اختر صفحة للانتقال إليها</p>
                                </div>
                                <div class="p-6 overflow-y-auto max-h-80">
                                    <div class="space-y-3">
                                        ${data.pages.map(page => `
                                            <div class="border border-gray-200 rounded-lg p-3 hover:border-green-800 hover:bg-gray-50 transition-colors">
                                                <button onclick="goToPage(${bookId}, ${page.page_number})" 
                                                        class="w-full text-right">
                                                    <div class="font-medium text-green-800 mb-1">
                                                        📄 صفحة ${page.page_number}
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        ${page.content_preview || 'لا يوجد معاينة متاحة'}
                                                    </div>
                                                </button>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-6 py-3 border-t">
                                    <div class="text-sm text-gray-600 text-center">
                                        إجمالي ${data.pagination.total} صفحة في هذا الكتاب
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.body.insertAdjacentHTML('beforeend', modal);
                }
            } catch (error) {
                alert('خطأ في تحميل الصفحات المشابهة');
            }
        }
        
        // نسخ المحتوى
        async function copyContent(resultId) {
            const contentDiv = document.querySelector(`.result-content-${resultId}`);
            const text = contentDiv.textContent;
            
            try {
                await navigator.clipboard.writeText(text);
                showToast('تم نسخ المحتوى بنجاح! 📋');
            } catch (error) {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showToast('تم نسخ المحتوى! 📋');
            }
        }
        

        
        // إغلاق النوافذ المنبثقة
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.remove();
            }
        }
        
        // عرض رسائل التأكيد
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            // إزالة الرسالة بعد 3 ثواني
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }, 3000);
        }
        
        // إضافة رسوم متحركة للخروج
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideOut {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(100%);
                }
            }
        `;
        document.head.appendChild(style);
        
        // إظهار نافذة الشرح
        function showHelpModal() {
            const helpModal = `
                <div id="help-modal" class="modal-overlay fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4" dir="rtl">
                    <div class="modal-content bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto  shadow-xl">
                        <!-- Header -->
                        <div class="bg-green-900 text-white p-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-white text-2xl font-bold">دليل البحث </h3>
                                <button onclick="closeModal('help-modal')" class="text-white hover:text-red-800 text-2xl font-bold">✕</button>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                            <!-- أنواع البحث الثلاثة -->
                            <section class="mb-8">
                                <h4 class="text-xl font-bold text-black mb-4 border-b-2 border-green-900 pb-2">أنواع البحث</h4>
                                <div class="space-y-4">
                                    <!-- البحث المطابق -->
                                    <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-4 h-4 bg-red-800 rounded-full"></div>
                                            <h5 class="font-bold text-black text-lg">البحث المطابق </h5>
                                        </div>
                                        <p class="text-gray-700">مطابقة حرفية دقيقة للنص كما كتبته بالضبط، بدون أي تغيير أو تعديل.</p>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <strong>مثال:</strong> البحث عن "الصلاة" سيجد فقط كلمة "الصلاة" بالضبط
                                        </div>
                                    </div>

                                    <!-- البحث المرن -->
                                    <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-4 h-4 bg-green-900 rounded-full"></div>
                                            <h5 class="font-bold text-black text-lg">البحث الغير مطابق </h5>
                                        </div>
                                        <p class="text-gray-700">يتجاهل أدوات التعريف (ال)، حروف العطف (و، ف)، علامات الترقيم، والهمزات المختلفة.</p>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <strong>مثال:</strong> البحث عن "صلاة" سيجد: "الصلاة"، "وصلاة"، "فصلاة"، "صلاه"، "صلاة،"
                                        </div>
                                    </div>

                                    <!-- البحث الصرفي -->
                                    <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-4 h-4 bg-black rounded-full"></div>
                                            <h5 class="font-bold text-black text-lg"> البحث الجذر الصرفي </h5>
                                        </div>
                                        <p class="text-gray-700">بحث بالجذر الصرفي للكلمة، يجد جميع المشتقات والتصريفات.</p>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <strong>مثال:</strong> البحث عن "صلى" سيجد: "صلاة"، "صلوات"، "يصلي"، "مصلى"، "صالح"
                                        </div>
                                    </div>
                                </div>
                            </section>
                          


                            <!-- ترتيب الكلمات -->
                            <section class="mb-8">
                                <h4 class="text-xl font-bold text-black mb-4 border-b-2 border-green-900 pb-2">ترتيب الكلمات</h4>
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="w-3 h-3 bg-red-800 rounded-full mt-1"></div>
                                        <div>
                                            <strong class="text-black">متتالية:</strong>
                                            <span class="text-gray-700">الكلمات يجب أن تكون متتابعة بدون فواصل</span>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="w-3 h-3 bg-green-900 rounded-full mt-1"></div>
                                        <div>
                                            <strong class="text-black">نفس الفقرة:</strong>
                                            <span class="text-gray-700">الكلمات في نفس الفقرة مع الحفاظ على السياق</span>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="w-3 h-3 bg-black rounded-full mt-1"></div>
                                        <div>
                                            <strong class="text-black">أي ترتيب:</strong>
                                            <span class="text-gray-700">الكلمات في أي مكان من النص بأي ترتيب</span>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- ترتيب النتائج -->
                            <section class="mb-8">
                                <h4 class="text-xl font-bold text-black mb-4 border-b-2 border-green-900 pb-2">ترتيب النتائج</h4>
                                <div class="space-y-3">
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <strong class="text-black">حسب الصلة:</strong>
                                        <span class="text-gray-700">النتائج الأكثر صلة بكلمات البحث تظهر أولاً</span>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <strong class="text-black">حسب التاريخ:</strong>
                                        <span class="text-gray-700">ترتيب النتائج حسب تاريخ النص أو المؤلف</span>
                                    </div>
                                </div>
                            </section>

                            <!-- الفلاتر -->
                            <section class="mb-6">
                                <h4 class="text-xl font-bold text-black mb-4 border-b-2 border-green-900 pb-2">الفلاتر المتاحة</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <strong class="text-black">القسم:</strong>
                                        <span class="text-gray-700">تصفية حسب قسم الكتاب</span>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <strong class="text-black">الكتاب:</strong>
                                        <span class="text-gray-700">تصفية حسب اسم الكتاب</span>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <strong class="text-black">المؤلف:</strong>
                                        <span class="text-gray-700">تصفية حسب اسم المؤلف</span>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <strong class="text-black">تاريخ الوفاة:</strong>
                                        <span class="text-gray-700">تصفية حسب فترة زمنية</span>
                                    </div>
                                </div>
                            </section>
                        </div>
                        
                        <!-- Footer -->
                        <div class="bg-gray-100 px-6 py-4 border-t">
                            <div class="text-center text-sm text-gray-600">
                                نظام البحث - مدعوم بتقنية Elasticsearch
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', helpModal);
        }
        
        // تحميل المزيد من النتائج
        async function loadMoreResults() {
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const query = document.getElementById('instantSearch').value.trim();
            
            if (!query) return;
            
            // تحديث زر التحميل
            loadMoreBtn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> جاري تحميل المزيد...';
            loadMoreBtn.disabled = true;
            
            // الحصول على الصفحة التالية من زر التحميل
            const currentPageMatch = loadMoreBtn.textContent.match(/الصفحة (\d+)/);
            const nextPage = currentPageMatch ? parseInt(currentPageMatch[1]) : 2;
            
            const perPage = document.getElementById('perPageSelect').value;
            const searchTypeInput = document.querySelector('input[name="searchType"]:checked');
            const searchType = searchTypeInput ? searchTypeInput.value : 'flexible_match';
            
            try {
                const params = new URLSearchParams({
                    q: query,
                    per_page: perPage,
                    page: nextPage,
                    search_type: searchType,
                    });
                
                const response = await fetch(`/api/ultra-search?${params}`);
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    // إزالة زر "تحميل المزيد" الحالي
                    loadMoreBtn.parentElement.remove();
                    
                    // إضافة النتائج الجديدة
                    // compute starting index for newly appended results
                    const existingCount = document.querySelectorAll('#searchResults .result-item').length;
                    const newResults = data.data.map((result, index) => `
                        <div id="result-${existingCount + index}" data-page-id="${result.id}" tabindex="0" data-result-index="${existingCount + index}" class="result-item result-card bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow mb-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <div class="text-xs text-gray-500 mb-2 text-left">ID: ${result.id}</div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                        <a href="${result.url ? result.url : ('/book/' + result.book_id + '/' + result.page_number)}" class="hover:text-emerald-600 transition-colors">${result.book_title || 'كتاب غير محدد'}</a>
                                    </h3>
                                    ${result.section ? `<div class="mt-2"><span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">${result.section}</span></div>` : ''}
                                        <div class="text-sm text-gray-600 flex items-center gap-3">
                                        <span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded text-xs">الصفحة ${result.page_number || ''}</span>
                                        ${result.author_name && result.author_name !== 'غير محدد' && result.author_name !== 'مؤلف' ? (result.author_id ? `<a href="/authors/${result.author_id}/details" class="inline-flex items-center gap-1 bg-green-50 hover:bg-green-100 px-2 py-1 rounded text-xs text-green-800 hover:text-green-900 transition-colors">✍️ ${result.author_name}</a>` : `<span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded text-xs text-gray-600">✍️ ${result.author_name}</span>`) : (result.author_name ? `<span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded text-xs text-gray-600">✍️ غير محدد</span>` : '')}
                                        ${result.section ? `<span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded text-xs">${result.section}</span>` : ''}
                                        ${result.chapter_title ? `<span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded text-xs">${result.chapter_title}</span>` : ''}
                                        ${result.volume_title ? `<span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded text-xs">${result.volume_title}</span>` : ''}
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded">
                                    النتيجة ${((nextPage - 1) * perPage) + index + 1}
                                </div>
                            </div>

                            <div class="text-gray-700 leading-relaxed mb-3">
                                <div class="result-content-${result.id}"><p>${result.highlight || result.content || 'لا يوجد محتوى للعرض'}</p></div>
                            </div>

                            <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-3 flex-row-reverse">
                                    <button onclick="toggleFullContent(${result.id})" class="toggle-btn toggle-btn-${result.id} px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">🔍 عرض الصفحة كاملة</button>
                                    <button onclick="copyContent(${result.id})" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">نسخ</button>
                                    <button onclick="shareResult(${result.id})" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">مشاركة</button>
                                    <button onclick="printContent(${result.id})" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">طباعة</button>
                                    <div class="relative inline-block">
                                        <button onclick="toggleMoreOptions(${result.id})" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">⋯</button>
                                        <div id="more-options-${result.id}" class="hidden more-options-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-20 border border-gray-200">
                                            <div class="py-1">
                                                <button onclick="showRelatedPages(${result.book_id}, ${result.page_number})" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📄 صفحات مشابهة</button>
                                                <button onclick="copyContent(${result.id})" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📋 نسخ النص</button>
                                                <button onclick="shareResult(${result.id})" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">🔗 مشاركة</button>
                                                <button onclick="printContent(${result.id})" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">🖨️ طباعة</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">ID: ${result.id}</div>
                            </div>

                            <div class="full-content-${result.id} hidden mt-4"><!-- will be inserted when requested --></div>
                        </div>
                    `).join('');
                    
                    // إضافة النتائج الجديدة للصفحة
                    document.getElementById('searchResults').insertAdjacentHTML('beforeend', newResults);

                    // re-init keyboard navigation for appended items
                    initKeyboardNav();
                    
                    // إضافة شريط الترقيم السابق/التالي بدلًا من زر "تحميل المزيد"
                    if (data.pagination && data.pagination.last_page && data.pagination.last_page > 1) {
                        document.getElementById('searchResults').insertAdjacentHTML('beforeend', `
                            <div id="paginationBar" class="flex items-center justify-center gap-4 mt-6">
                                <button onclick="window.searchInstance.goToPage(${Math.max(1, data.pagination.current_page - 1)})" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm" ${data.pagination.current_page === 1 ? 'disabled' : ''}>السابق</button>
                                <div class="text-sm text-gray-700">الصفحة ${data.pagination.current_page} من ${data.pagination.last_page} • إجمالي ${data.pagination.total} نتيجة</div>
                                <button onclick="window.searchInstance.goToPage(${Math.min(data.pagination.last_page, data.pagination.current_page + 1)})" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm" ${data.pagination.current_page === data.pagination.last_page ? 'disabled' : ''}>التالي</button>
                            </div>
                        `);
                    }
                    
                    showToast(`تم تحميل ${data.data.length} نتيجة إضافية! 📄`);
                } else {
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.innerHTML = '📄 لا توجد نتائج إضافية';
                    showToast('لا توجد نتائج إضافية للعرض');
                }
            } catch (error) {
                console.error('Error loading more results:', error);
                showToast('خطأ في تحميل المزيد من النتائج');
                loadMoreBtn.disabled = false;
                loadMoreBtn.innerHTML = '📄 تحميل المزيد من النتائج';
            }
        }
        
        // وظيفة مساعدة لتسميات أنواع البحث
        function getSearchModeLabel(mode) {
            const labels = {
                'flexible_match': 'مرن',
                'exact_match': 'مطابق',
                'morphological': 'صرفي'
            };
            return labels[mode] || 'مرن';
        }
        
        // إخفاء القوائم عند النقر خارجها
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick*="toggleMoreOptions"]') && !event.target.closest('[id^="more-options-"]')) {
                document.querySelectorAll('[id^="more-options-"]').forEach(div => {
                    div.classList.add('hidden');
                });
            }
        });

        // ---- Keyboard navigation between results ----
        let keyboardNav = {
            focusedIndex: -1,
        };

        function initKeyboardNav() {
            const items = Array.from(document.querySelectorAll('#searchResults .result-item'));
            items.forEach((el, idx) => {
                el.setAttribute('data-result-index', idx);
                el.tabIndex = 0;

                // click/focus handlers to keep track
                el.addEventListener('focus', () => {
                    setFocusedIndex(idx);
                });
            });

            // if none focused, set first as focused when items exist
            if (items.length && keyboardNav.focusedIndex === -1) {
                setFocusedIndex(0);
            }
        }

        function setFocusedIndex(idx) {
            const items = Array.from(document.querySelectorAll('#searchResults .result-item'));
            if (!items.length) return;
            if (idx < 0) idx = 0;
            if (idx >= items.length) idx = items.length - 1;

            // remove previous
            items.forEach(i => i.classList.remove('result-selected'));
            const el = items[idx];
            el.classList.add('result-selected');
                    el.focus({ preventScroll: true });
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            keyboardNav.focusedIndex = idx;
        }

        document.addEventListener('keydown', function(e) {
            const items = Array.from(document.querySelectorAll('#searchResults .result-item'));
            
            // التنقل بين النتائج بالأسهم أعلى وأسفل فقط
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                setFocusedIndex((keyboardNav.focusedIndex === -1 ? 0 : keyboardNav.focusedIndex + 1));
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                setFocusedIndex((keyboardNav.focusedIndex === -1 ? 0 : keyboardNav.focusedIndex - 1));
            } else if (e.key === 'Enter') {
                // trigger full page view for focused item
                const idx = keyboardNav.focusedIndex;
                if (idx >= 0 && items[idx]) {
                    // find the result id from the element's inner toggle button class
                    const el = items[idx];
                    const toggleBtn = el.querySelector('[class*="toggle-btn-"]');
                    if (toggleBtn) {
                        // extract id from class name
                        const cls = Array.from(toggleBtn.classList).find(c => c.startsWith('toggle-btn-'));
                        if (cls) {
                            const id = cls.replace('toggle-btn-','');
                            toggleFullContent(id);
                        }
                    }
                }
            }
        });
        
        // تهيئة البحث عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            window.ultraFastSearch = new UltraFastSearch();
        window.searchInstance = window.ultraFastSearch; // للتوافق مع الكود الموجود
            
            // إذا جاء المستخدم من صفحة الـ home مع نص بحث، قم بالبحث تلقائياً
            const urlParams = new URLSearchParams(window.location.search);
            const query = urlParams.get('q');
            const searchTypeParam = urlParams.get('search_type');
            
            if (query && query.trim()) {
                // إذا كان هناك نوع بحث محدد من URL، حدده
                if (searchTypeParam && ['exact_match', 'flexible_match', 'morphological'].includes(searchTypeParam)) {
                    const radioButton = document.querySelector(`input[name="searchType"][value="${searchTypeParam}"]`);
                    if (radioButton) radioButton.checked = true;
                }
                
                // تأكد من أن النص في مربع البحث ثم قم بالبحث
                setTimeout(() => {
                    if (window.searchInstance.searchInput.value.trim()) {
                        window.searchInstance.performSearch(window.searchInstance.searchInput.value.trim());
                    }
                }, 500);
            }

            // init keyboard navigation handlers (if results present later)
            initKeyboardNav();
        });

        // التنقل بالأسهم يمين/يسار فقط في عرض المحتوى الكامل
        document.addEventListener('keydown', function(e) {
            // التحقق من وجود محتوى كامل مفتوح
            const currentFullContent = document.querySelector('[class*="full-content-"]:not(.hidden)');
            
            if (currentFullContent) {
                // استخراج معرف الصفحة من class name
                const classNames = currentFullContent.className.split(' ');
                const fullContentClass = classNames.find(cls => cls.startsWith('full-content-'));
                
                if (fullContentClass) {
                    const pageId = fullContentClass.replace('full-content-', '');
                    
                    // البحث عن أزرار التنقل في المحتوى الكامل
                    const prevButton = currentFullContent.querySelector('button[onclick*="navigateToPage"][onclick*="- 1"]');
                    const nextButton = currentFullContent.querySelector('button[onclick*="navigateToPage"][onclick*="+ 1"]');
                    
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        // التنقل للصفحة السابقة
                        if (prevButton && !prevButton.disabled) {
                            prevButton.click();
                        }
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        // التنقل للصفحة التالية
                        if (nextButton && !nextButton.disabled) {
                            nextButton.click();
                        }
                    }
                }
            }
        });
    </script>

    <!-- Filter Modal -->
    <div id="filterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[80vh] overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 id="filterModalTitle" class="text-lg font-semibold text-gray-900">فلترة حسب القسم</h3>
                <button type="button" id="closeFilterModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-4">
                <!-- Search input for filter options -->
                <div class="mb-4" id="filterSearchContainer">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="filterSearch" 
                            placeholder="البحث في الخيارات..."
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Date range inputs for death date filter -->
                <div id="dateRangeContainer" class="hidden mb-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">من سنة</label>
                        <input type="number" id="deathYearFrom" placeholder="مثال: 1200" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">إلى سنة</label>
                        <input type="number" id="deathYearTo" placeholder="مثال: 1400" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <!-- Filter options list -->
                <div id="filterOptionsList" class="max-h-60 overflow-y-auto space-y-1">
                    <!-- Options will be dynamically loaded here -->
                </div>
            </div>
            
            <div class="flex items-center justify-between p-4 border-t border-gray-200">
                <button type="button" id="clearFilterSelection" class="text-sm text-gray-600 hover:text-gray-800">
                    مسح التحديد
                </button>
                <div class="flex gap-2">
                    <button type="button" id="cancelFilterModal" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                        إلغاء
                    </button>
                    <button type="button" id="applyFilterModal" class="px-4 py-2 bg-green-800 text-white text-sm rounded-md hover:bg-green-900">
                        تطبيق
                    </button>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>
