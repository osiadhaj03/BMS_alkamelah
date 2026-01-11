@extends('layouts.app')

@section('title', 'المقالات')

@section('content')
<div class="container mx-auto px-4 py-8" dir="rtl">
    <h1 class="text-4xl font-bold mb-8 text-right" style="color: #2C6E4A;">المقالات</h1>

    <!-- المقالات المميزة -->
    @if($featuredArticles->count() > 0)
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-right flex items-center gap-2" style="color: #BA4749;">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            مقالات مميزة
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($featuredArticles as $article)
                @include('components.news.article-card', ['article' => $article, 'featured' => true])
            @endforeach
        </div>
    </div>
    @endif

    <!-- كل المقالات -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($allArticles as $article)
            @include('components.news.article-card', ['article' => $article])
        @empty
            <div class="col-span-3 text-center py-12">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-xl text-gray-500">لا توجد مقالات حالياً</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($allArticles->hasPages())
    <div class="mt-8">
        {{ $allArticles->links() }}
    </div>
    @endif
</div>
@endsection
