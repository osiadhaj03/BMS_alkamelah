@extends('layouts.app')

@section('title', $article->title)

@section('content')
<div class="container mx-auto px-4 py-8" dir="rtl">
    <article class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- صورة الغلاف -->
        @if($article->cover_image)
        <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-96 object-cover">
        @endif

        <div class="p-8">
            <!-- Header -->
            <header class="mb-8">
                <div class="flex items-center gap-4 mb-4 flex-wrap">
                    <span class="px-4 py-2 text-white rounded-md text-sm font-bold" style="background-color: #2C6E4A;">
                        {{ $article->category_name }}
                    </span>
                    @if($article->is_featured)
                    <span class="px-4 py-2 text-white rounded-md text-sm font-bold" style="background-color: #BA4749;">
                        مميز
                    </span>
                    @endif
                    <span class="text-gray-500 text-sm">{{ $article->published_at->diffForHumans() }}</span>
                    @if($article->reading_time)
                    <span class="text-gray-500 text-sm">{{ $article->reading_time }} دقيقة قراءة</span>
                    @endif
                </div>
                
                <h1 class="text-4xl font-bold text-right mb-4" style="color: #1A3A2A;">{{ $article->title }}</h1>
                
                @if($article->excerpt)
                <p class="text-xl text-gray-600 text-right leading-relaxed border-r-4 pr-4" style="border-color: #2C6E4A;">
                    {{ $article->excerpt }}
                </p>
                @endif

                <!-- Author & Stats -->
                <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        @if($article->author)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $article->author->name }}
                        </span>
                        @elseif($article->author_name)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $article->author_name }}
                        </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ number_format($article->views_count) }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            {{ number_format($article->likes_count) }}
                        </span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="prose prose-lg max-w-none text-right prose-headings:text-right prose-p:text-right" style="direction: rtl;">
                {!! $article->content !!}
            </div>

            <!-- Tags -->
            @if($article->tags && count($article->tags) > 0)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-bold mb-3 text-right">الكلمات المفتاحية:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">#{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Related Content -->
            @if($article->relatedBook || $article->relatedAuthor)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-bold mb-3 text-right">محتوى ذو صلة:</h3>
                <div class="flex flex-col gap-2">
                    @if($article->relatedBook)
                    <a href="{{ route('book.read', $article->relatedBook->id) }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        الكتاب: {{ $article->relatedBook->title }}
                    </a>
                    @endif
                    @if($article->relatedAuthor)
                    <a href="{{ route('author.show', $article->relatedAuthor->id) }}" class="text-blue-600 hover:underline flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        المؤلف: {{ $article->relatedAuthor->name }}
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Back Button -->
            <div class="mt-8">
                <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg font-bold transition-colors" style="background-color: #2C6E4A;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                    العودة إلى المقالات
                </a>
            </div>
        </div>
    </article>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6 text-right" style="color: #2C6E4A;">مقالات ذات صلة</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedArticles as $relatedArticle)
                @include('components.news.article-card', ['article' => $relatedArticle])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
