<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $featuredArticles = Article::published()->featured()->orderBy('priority', 'desc')->take(3)->get();
        $allArticles = Article::published()->orderBy('published_at', 'desc')->paginate(12);
        
        return view('pages.articles.index', compact('featuredArticles', 'allArticles'));
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->with(['author', 'relatedBook', 'relatedAuthor', 'approvedComments'])
            ->published()
            ->firstOrFail();
        
        // Increment views
        $article->incrementViews();
        
        // Get related articles
        $relatedArticles = Article::published()
            ->where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->limit(3)
            ->get();
        
        return view('pages.articles.show', compact('article', 'relatedArticles'));
    }

    public function byCategory($category)
    {
        $featuredArticles = collect();
        $allArticles = Article::published()->byCategory($category)->orderBy('published_at', 'desc')->paginate(12);
        
        return view('pages.articles.index', compact('allArticles', 'featuredArticles'));
    }

    public function like(Article $article)
    {
        $article->incrementLikes();
        
        return response()->json([
            'success' => true,
            'likes_count' => $article->likes_count
        ]);
    }
}
