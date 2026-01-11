@extends('layouts.app')

@section('title', 'الأخبار')

@section('content')
    <!-- Header -->
    @include('components.layout.header')

    <div class="min-h-screen bg-[#fafafa] relative overflow-hidden">
        <!-- Section Background Pattern -->
        <div class="absolute inset-0 pointer-events-none"
            style="background-image: url('{{ asset('assets/Frame 1321314420.png') }}'); background-repeat: repeat; background-size: 800px; background-attachment: fixed;">
        </div>

        <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20" dir="rtl">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center">
                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20" style="color: #2C6E4A;">
                            <path d="M2 3a1 1 0 011-1h12a1 1 0 011 1H2zm0 4h16v10a2 2 0 01-2 2H4a2 2 0 01-2-2V7zm2 5a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zm0 4a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-extrabold text-[#1a3a2a]">آخر الأخبار</h1>
                </div>
            </div>

            <!-- الأخبار المثبتة -->
            @if($pinnedNews->count() > 0)
            <div class="mb-12">
                <h2 class="text-2xl font-bold mb-4 text-right flex items-center gap-2" style="color: #BA4749;">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    أخبار مهمة
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($pinnedNews as $news)
                        @include('components.news.news-card', ['news' => $news, 'featured' => true])
                    @endforeach
                </div>
            </div>
            @endif

            <!-- كل الأخبار -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($allNews as $news)
                    @include('components.news.news-card', ['news' => $news])
                @empty
                    <div class="col-span-3 text-center py-12">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        <p class="text-xl text-gray-500">لا توجد أخبار حالياً</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($allNews->hasPages())
            <div class="mt-8">
                {{ $allNews->links() }}
            </div>
            @endif
        </section>
    </div>

    <!-- Footer -->
    @include('components.layout.footer')
