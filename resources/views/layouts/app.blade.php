<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <title>@yield('seo_title', 'المكتبة الكاملة | أكبر مشروع لرقمنة التراث العلمي والأدبي')</title>
    <meta name="title" content="@yield('seo_title', 'المكتبة الكاملة | أكبر مشروع لرقمنة التراث العلمي والأدبي')">
    <meta name="description" content="@yield('seo_description', 'استكشف آلاف الكتب في الفقه، الأدب، التاريخ والأنساب. المكتبة الكاملة توفر بحثاً ذكياً في المحتوى ووصولاً سهلاً لأمهات الكتب العربية.')">
    <meta name="keywords" content="مكتبة رقمية, كتب عربية, تراث إسلامي, فقه, تاريخ, أدب, بحث في الكتب, مؤلفين عرب, المكتبة الكاملة">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Arabic">
    <meta name="author" content="BMS - المكتبة الكاملة">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('seo_title', 'المكتبة الكاملة | أكبر مشروع لرقمنة التراث العلمي والأدبي')">
    <meta property="og:description" content="@yield('seo_description', 'استكشف آلاف الكتب في الفقه، الأدب، التاريخ والأنساب. المكتبة الكاملة توفر بحثاً ذكياً في المحتوى.')">
    <meta property="og:image" content="{{ asset('images/المكتبة الكاملة.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('seo_title', 'المكتبة الكاملة | أكبر مشروع لرقمنة التراث العلمي والأدبي')">
    <meta property="twitter:description" content="@yield('seo_description', 'استكشف آلاف الكتب في الفقه، الأدب، التاريخ والأنساب. المكتبة الكاملة توفر بحثاً ذكياً في المحتوى.')">
    <meta property="twitter:image" content="{{ asset('images/المكتبة الكاملة.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Schema.org (JSON-LD) for AI Engines -->
    @stack('schema')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">


    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS (CDN for prototype speed) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                        serif: ['Amiri', 'serif'],
                    },
                    colors: {
                        green: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Cairo', sans-serif; }
        .font-serif { font-family: 'Amiri', serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

        /* Alpine.js cloak */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <x-layout.announcement-banner />
    @yield('content')
    
    <x-chatbot />

    @stack('scripts')
</body>
</html>
