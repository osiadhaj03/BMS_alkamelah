<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Main Pages -->
    <url>
        <loc>{{ url('/category') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>{{ url('/books') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>{{ url('/authors') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc>{{ url('/news') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>{{ url('/articles') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc>{{ url('/about-us') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <url>
        <loc>{{ url('/search') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc>{{ url('/feedback') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    
    <!-- Categories -->
    @forelse($categories as $category)
    <url>
        <loc>{{ url('/category/' . $category->id) }}</loc>
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @empty
    @endforelse
    
    <!-- Authors -->
    @forelse($authors as $author)
    <url>
        <loc>{{ url('/author/' . $author->id) }}</loc>
        <lastmod>{{ $author->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @empty
    @endforelse
    
    <!-- News Articles -->
    @forelse($news as $newsItem)
    <url>
        <loc>{{ url('/news/' . $newsItem->slug) }}</loc>
        <lastmod>{{ $newsItem->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @empty
    @endforelse
    
    <!-- Blog Articles -->
    @forelse($articles as $article)
    <url>
        <loc>{{ url('/articles/' . $article->slug) }}</loc>
        <lastmod>{{ $article->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @empty
    @endforelse
    
    <!-- Books (الكتب) -->
    @forelse($books as $book)
    <url>
        <loc>{{ url('/books/' . $book->id) }}</loc>
        <lastmod>{{ $book->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.85</priority>
    </url>
    @empty
    @endforelse
    
    <!-- Chapters (الفصول) -->
    @forelse($chapters as $chapter)
    <url>
        <loc>{{ url('/books/' . $chapter->book_id . '/chapters/' . $chapter->id) }}</loc>
        <lastmod>{{ $chapter->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @empty
    @endforelse
</urlset>