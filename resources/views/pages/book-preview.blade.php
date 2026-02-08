<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} | المكتبة الكاملة</title>    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Arabic Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS Variables -->
    <style>
        :root {
            --bg-body: #ffffff; 
            --bg-paper: #fdfbf7;
            --bg-sidebar: #ffffff;
            --bg-hover: #f5f5f5;
            --text-main: #2b2b2b;
            --text-secondary: #666666;
            --text-muted: #9ca3af;
            --accent-color: #15803d; 
            --accent-hover: #16a34a;
            --accent-light: #dcfce7;
            --border-color: #e5e7eb;
            --highlight-color: #ffeb3b80;
            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-paper: 0 4px 20px rgba(0,0,0,0.08);
            --shadow-dropdown: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --font-main: 'Amiri', serif;       
            --font-ui: 'Tajawal', sans-serif;  
            --radius-main: 16px;
        }
        
        body { 
            font-family: var(--font-ui); 
            background-color: var(--bg-body);
            color: var(--text-main);
        }
        
        .bg-green-600 { background-color: #16a34a; }
        .text-green-600 { color: #16a34a; }
        .text-green-700 { color: #15803d; }
        .bg-green-700 { background-color: #15803d; }
        .border-green-600 { border-color: #16a34a; }
        .hover\:bg-green-700:hover { background-color: #15803d; }
        .hover\:text-green-600:hover { color: #16a34a; }
        .hover\:border-green-600:hover { border-color: #16a34a; }
        .focus\:border-green-600:focus { border-color: #16a34a; }
        
        /* Alpine.js x-cloak - Hide elements until Alpine initializes */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen" style="background-color: var(--bg-body);">
    <div dir="rtl">

        <!-- Main Site Header -->
        <x-layout.header />

        <!-- Content Issues Alert -->
        <div x-data="{ show: true }" x-show="show" class="bg-yellow-50 border-b border-yellow-200 p-2 md:p-4 relative z-40">
            <div class="flex items-center justify-center container mx-auto px-4">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="mr-3 flex-1 md:flex-none text-center">
                    <p class="text-xs md:text-sm font-medium text-yellow-800">
                        اذا واجهت اي مشكلة في محتوى الكتاب من صفحات و الفصول
                        <a href="{{ route('feedback', ['type' => 'complaint', 'category' => 'book', 'subject' => 'مشكلة في محتوى الكتاب: ' . ($book->title ?? ''), 'message' => 'رابط الكتاب: ' . Request::url()]) }}" class="font-bold underline hover:text-yellow-900 mr-1">
                            يرجى الابلاغ عنها
                        </a>
                    </p>
                </div>
                <div class="mr-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false" type="button" class="inline-flex bg-yellow-50 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                            <span class="sr-only">إغلاق</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Book Hero Section - Pass book data -->
        <x-book.header-book 
            :book="$book ?? null" 
        />
        
        <!-- Main Layout -->
        <div class="flex" style="padding-top: 0px;">
            <!-- Sidebar Component - Pass chapters data -->
            <x-book.table-of-contents 
                :chapters="$chapters ?? collect()" 
                :book="$book ?? null"
                :currentPage="$currentPage ?? null"
            />
            
            <!-- Main Content Component - Pass page data -->
            <x-book.book-content 
                :currentPage="$currentPage ?? null"
                :pages="$pages ?? collect()"
                :book="$book ?? null"
                :currentPageNum="$currentPageNum ?? 1"
                :totalPages="$totalPages ?? 1"
                :nextPage="$nextPage ?? null"
                :previousPage="$previousPage ?? null"
            />
        </div>
        
        <!-- Progress Component - Pass progress data -->
        <x-book.reading-progress 
            :currentPageNum="$currentPageNum ?? 1"
            :totalPages="$totalPages ?? 1"
            :book="$book ?? null"
            :currentPage="$currentPage ?? null"
            :nextPage="$nextPage ?? null"
            :previousPage="$previousPage ?? null"
        />
    </div>
</body>
</html>