<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} </title> 
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body class="min-h-screen" style="background-color: var(--bg-body);">
    <div dir="rtl">

        <!-- Main Site Header -->
        <x-layout.header />
        
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
            :nextPage="$nextPage ?? null"
            :previousPage="$previousPage ?? null"
        />
    </div>
</body>
</html>