@extends('layouts.app')

@section('title', $author->full_name)

@section('content')
    <!-- Header -->
    @include('components.layout.header')

    <div class="min-h-screen bg-[#fafafa] relative overflow-hidden">
        <!-- Section Background Pattern -->
        <div class="absolute inset-0 pointer-events-none"
            style="background-image: url('{{ asset('assets/Frame 1321314420.png') }}'); background-repeat: repeat; background-size: 800px; background-attachment: fixed;">
        </div>

        <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20" dir="rtl">

            <!-- Author Header Card -->
            <div
                class="bg-white rounded-[2rem] shadow-xl shadow-green-900/5 overflow-hidden border border-gray-100 p-8 md:p-12 mb-8">
                <div class="flex flex-col md:flex-row gap-8 items-start">

                    <!-- Author Image -->
                    <div class="flex-shrink-0">
                        @if($author->image)
                            <img src="{{ asset($author->image) }}" alt="{{ $author->full_name }}"
                                class="w-32 h-32 md:w-40 md:h-40 rounded-2xl object-cover shadow-lg">
                        @else
                            <div
                                class="w-32 h-32 md:w-40 md:h-40 rounded-2xl bg-gradient-to-br from-[#2C6E4A] to-[#4A9B6D] flex items-center justify-center shadow-lg">
                                <svg class="w-16 h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Author Info -->
                    <div class="flex-1">
                        <h1 class="text-3xl md:text-4xl font-bold text-[#1a3a2a] mb-4">{{ $author->full_name }}</h1>

                        <!-- Meta Info -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            @if($author->madhhab)
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-[#e8f5e9] text-[#2C6E4A]">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                    {{ $author->madhhab }}
                                </span>
                            @endif

                            @if($author->death_date)
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gray-100 text-gray-700">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    توفي: {{ $author->death_date->format('Y') }} هـ
                                </span>
                            @elseif($author->is_living)
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-blue-100 text-blue-700">
                                    <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="4"></circle>
                                    </svg>
                                    معاصر
                                </span>
                            @endif

                            <span
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-amber-100 text-amber-700">
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                                {{ $books->total() }} كتاب
                            </span>
                        </div>

                        <!-- Laqab & Kunyah -->
                        @if($author->laqab || $author->kunyah)
                            <div class="text-gray-600 mb-4">
                                @if($author->laqab)
                                    <span class="font-semibold">اللقب:</span> {{ $author->laqab }}
                                @endif
                                @if($author->laqab && $author->kunyah) | @endif
                                @if($author->kunyah)
                                    <span class="font-semibold">الكنية:</span> {{ $author->kunyah }}
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Biography Card -->
            @if($author->biography)
                <div
                    class="bg-white rounded-[2rem] shadow-xl shadow-green-900/5 overflow-hidden border border-gray-100 p-8 md:p-12 mb-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 flex items-center justify-center bg-[#e8f5e9] rounded-xl">
                            <svg class="w-5 h-5 text-[#2C6E4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-[#1a3a2a]">السيرة الذاتية</h2>
                    </div>

                    <div class="prose prose-lg max-w-none text-gray-700 leading-loose"
                        style="font-size: 1.1rem; line-height: 2;">
                        {!! nl2br(e($author->biography)) !!}
                    </div>
                </div>
            @endif

            <!-- Books Section -->
            <div
                class="bg-white rounded-[2rem] shadow-xl shadow-green-900/5 overflow-hidden border border-gray-100 p-8 md:p-12">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center bg-[#e8f5e9] rounded-xl">
                            <svg class="w-5 h-5 text-[#2C6E4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-[#1a3a2a]">كتب المؤلف</h2>
                    </div>
                    <span class="text-gray-500">{{ $books->total() }} كتاب</span>
                </div>

                <!-- Books Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-right font-medium text-gray-900 uppercase tracking-wider w-16"
                                    style="font-size: 1rem;">#</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right font-medium text-gray-900 uppercase tracking-wider"
                                    style="font-size: 1rem;">اسم الكتاب</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right font-medium text-gray-900 uppercase tracking-wider w-40"
                                    style="font-size: 1rem;">القسم</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center font-medium text-gray-900 uppercase tracking-wider w-28"
                                    style="font-size: 1rem;">الصفحات</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center font-medium text-gray-900 uppercase tracking-wider w-28"
                                    style="font-size: 1rem;">المجلدات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($books as $index => $book)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-900 w-16" style="font-size: 1.1rem;">
                                        {{ ($books->currentPage() - 1) * $books->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('book.read', $book->id) }}"
                                            class="font-medium text-[#2C6E4A] hover:text-green-900 hover:underline transition-colors"
                                            style="font-size: 1.1rem;">
                                            {{ $book->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 w-40" style="font-size: 1rem;">
                                        {{ $book->bookSection->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center w-28">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ number_format($book->pages_count ?? 0) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center w-28">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                            {{ $book->volumes_count ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        لا توجد كتب لهذا المؤلف
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($books->hasPages())
                    <div class="mt-6 flex justify-center">
                        <nav class="flex items-center gap-2" dir="ltr">
                            {{-- Previous Page Link --}}
                            @if ($books->onFirstPage())
                                <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">السابق</span>
                            @else
                                <a href="{{ $books->previousPageUrl() }}" class="px-4 py-2 text-[#2C6E4A] bg-white border border-[#2C6E4A] rounded-lg hover:bg-[#2C6E4A] hover:text-white transition-colors">السابق</a>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                                @if ($page == $books->currentPage())
                                    <span class="px-4 py-2 bg-[#2C6E4A] text-white rounded-lg font-bold">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($books->hasMorePages())
                                <a href="{{ $books->nextPageUrl() }}" class="px-4 py-2 text-[#2C6E4A] bg-white border border-[#2C6E4A] rounded-lg hover:bg-[#2C6E4A] hover:text-white transition-colors">التالي</a>
                            @else
                                <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">التالي</span>
                            @endif
                        </nav>
                    </div>
                    <div class="mt-3 text-center text-gray-500 text-sm">
                        عرض {{ $books->firstItem() }} - {{ $books->lastItem() }} من {{ $books->total() }} كتاب
                    </div>
                @endif
            </div>

            <!-- Back Button -->
            <div class="mt-8 text-center">
                <a href="{{ route('authors.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white text-[#2C6E4A] border-2 border-[#2C6E4A] rounded-full font-bold hover:bg-[#2C6E4A] hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    العودة لقائمة المؤلفين
                </a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    @include('components.layout.footer')
@endsection