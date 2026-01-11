<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 {{ $featured ?? false ? 'border-2' : '' }}" style="{{ $featured ?? false ? 'border-color: #BA4749;' : '' }}">
    @if($article->cover_image)
    <a href="{{ route('articles.show', $article->slug) }}">
        <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover hover:opacity-90 transition-opacity">
    </a>
    @endif
    
    <div class="p-6">
        <div class="flex items-center gap-2 mb-3 flex-wrap">
            <span class="text-xs px-3 py-1 text-white rounded-full font-bold" style="background-color: #2C6E4A;">
                {{ $article->category_name }}
            </span>
            @if($article->is_featured)
            <span class="text-xs px-3 py-1 text-white rounded-full font-bold flex items-center gap-1" style="background-color: #BA4749;">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                مميز
            </span>
            @endif
            @if($article->reading_time)
            <span class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                {{ $article->reading_time }} دقيقة
            </span>
            @endif
        </div>
        
        <h3 class="text-xl font-bold mb-2 text-right line-clamp-2" style="color: #1A3A2A;">
            <a href="{{ route('articles.show', $article->slug) }}" class="hover:underline" style="color: #1A3A2A;">
                {{ $article->title }}
            </a>
        </h3>
        
        @if($article->excerpt)
        <p class="text-gray-600 mb-4 text-right line-clamp-3 leading-relaxed">
            {{ $article->excerpt }}
        </p>
        @endif
        
        <div class="flex justify-between items-center text-sm text-gray-500">
            <div class="flex items-center gap-3">
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
            <span>{{ $article->published_at->diffForHumans() }}</span>
        </div>
    </div>
</div>
