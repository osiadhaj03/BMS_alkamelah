<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="{{ session('book_reader_settings.theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    
    <title>{{ $title ?? 'قارئ الكتب' }} - المكتبة الكاملة</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Tajawal:wght@300;400;500;700&family=Noto+Kufi+Arabic:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Theme CSS Variables -->
    <style>
        /* ═══════════════════════════════════════════════════════════ */
        /* CSS Variables - Theme Colors */
        /* ═══════════════════════════════════════════════════════════ */
        
        :root,
        [data-theme="light"] {
            --reader-bg: #f5f5f5;
            --reader-bg-rgb: 245, 245, 245;
            --card-bg: #ffffff;
            --paper-bg: #ffffff;
            --reader-text: #1f2937;
            --reader-text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --hover-bg: rgba(0, 0, 0, 0.04);
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: rgba(37, 99, 235, 0.1);
            --highlight-bg: rgba(250, 204, 21, 0.4);
            --font-primary: 'Amiri';
        }
        
        [data-theme="dark"] {
            --reader-bg: #121212;
            --reader-bg-rgb: 18, 18, 18;
            --card-bg: #1e1e1e;
            --paper-bg: #252525;
            --reader-text: #e5e5e5;
            --reader-text-secondary: #9ca3af;
            --border-color: #374151;
            --hover-bg: rgba(255, 255, 255, 0.08);
            --primary-color: #60a5fa;
            --primary-hover: #3b82f6;
            --primary-light: rgba(96, 165, 250, 0.15);
            --highlight-bg: rgba(250, 204, 21, 0.3);
        }
        
        [data-theme="sepia"] {
            --reader-bg: #f8f0e3;
            --reader-bg-rgb: 248, 240, 227;
            --card-bg: #fdf6e8;
            --paper-bg: #fefcf5;
            --reader-text: #3d3223;
            --reader-text-secondary: #6b5d4a;
            --border-color: #d4c4a8;
            --hover-bg: rgba(101, 78, 43, 0.08);
            --primary-color: #8b5a2b;
            --primary-hover: #6d4420;
            --primary-light: rgba(139, 90, 43, 0.12);
            --highlight-bg: rgba(250, 204, 21, 0.35);
        }
        
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body.book-reader-body {
            font-family: 'Amiri', 'Tajawal', serif;
            background: var(--reader-bg);
            color: var(--reader-text);
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Hide scrollbar but keep functionality */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--reader-text-secondary);
        }
        
        /* Hide elements before Alpine.js loads */
        [x-cloak] {
            display: none !important;
        }
    </style>
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    @stack('styles')
</head>
<body class="book-reader-body">
    {{ $slot }}
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Book Reader Alpine Component -->
    <script>
        @include('livewire.book-reader.partials.alpine-component')
    </script>
    
    @stack('scripts')
</body>
</html>
