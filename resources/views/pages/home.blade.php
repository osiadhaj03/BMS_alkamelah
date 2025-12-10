<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مكتبة الكاملة - الصفحة الرئيسية</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'arabic': ['Arial', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Arabic', Arial, sans-serif;
        }
    </style>
    
    @livewireStyles
</head>
<body class="bg-gray-50">

    <!-- Header -->
    @include('components.layout.header')

    <!-- Hero Section -->
    @include('components.layout.hero')

    @include('components.layout.category')

    <!-- Books Section -->
    @include('components.layout.books-section')

    <!-- Authors Section -->
    @include('components.layout.authors-section')

    <!-- Footer -->
    @include('components.layout.footer')

    @livewireScripts
</body>
</html>