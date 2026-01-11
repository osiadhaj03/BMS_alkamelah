@extends('layouts.app')

@section('title', $news->title)

@section('content')
    <!-- Header -->
    @include('components.layout.header')

    <div class="min-h-screen bg-[#fafafa] relative overflow-hidden">
        <!-- Section Background Pattern -->
        <div class="absolute inset-0 pointer-events-none"
            style="background-image: url('{{ asset('assets/Frame 1321314420.png') }}'); background-repeat: repeat; background-size: 800px; background-attachment: fixed;">
        </div>

        <section class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12" dir="rtl">
            <article class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- الصورة المميزة -->
                @if($news->featured_image)
                <img src="{{ Storage::url($news->featured_image) }}" alt="{{ $news->title }}" class="w-full h-96 object-cover">
                @endif

                <div class="p-8">
                    <!-- العنوان والمعلومات -->
                    <header class="mb-8">
                        <div class="flex items-center gap-4 mb-4 flex-wrap">
                            <span class="px-4 py-2 text-white rounded-md text-sm font-bold" style="background-color: #2C6E4A;">
                                {{ $news->category_name }}
                            </span>
                            @if($news->is_pinned)
                            <span class="px-4 py-2 text-white rounded-md text-sm font-bold" style="background-color: #BA4749;">
                                مثبت
                            </span>
                            @endif
                            <span class="text-gray-500 text-sm">{{ $news->published_at->diffForHumans() }}</span>
                            <span class="text-gray-500 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ number_format($news->views_count) }} مشاهدة
                            </span>
                        </div>
                        
                        <h1 class="text-4xl font-bold text-right mb-4" style="color: #1A3A2A;">{{ $news->title }}</h1>
                        
                        @if($news->excerpt)
                        <p class="text-xl text-gray-600 text-right leading-relaxed border-r-4 pr-4" style="border-color: #2C6E4A;">
                            {{ $news->excerpt }}
                        </p>
                        @endif
                    </header>

                    <!-- المحتوى -->
                    <div class="prose prose-lg max-w-none text-right prose-headings:text-right prose-p:text-right" style="direction: rtl;">
                        {!! $news->content !!}
                    </div>

                    <!-- معلومات إضافية -->
                    <footer class="mt-12 pt-8 border-t border-gray-200">
                        <div class="flex justify-between items-center flex-wrap gap-4">
                            <div class="text-sm text-gray-500">
                                نُشر في: {{ $news->published_at->format('Y-m-d h:i A') }}
                            </div>
                            @if($news->author)
                            <div class="text-sm text-gray-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                بواسطة: {{ $news->author->name }}
                            </div>
                            @endif
                        </div>
                    </footer>

                    <!-- العودة للأخبار -->
                    <div class="mt-8">
                        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg font-bold transition-colors" style="background-color: #2C6E4A;" onmouseover="this.style.backgroundColor='#1A3A2A'" onmouseout="this.style.backgroundColor='#2C6E4A'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                            العودة إلى الأخبار
                        </a>
                    </div>
                </div>
            </article>
        </section>
    </div>

    <!-- Footer -->
    @include('components.layout.footer')
