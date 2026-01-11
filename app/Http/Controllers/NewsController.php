<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $pinnedNews = News::published()->pinned()->orderBy('priority', 'desc')->take(2)->get();
        $allNews = News::published()->orderBy('published_at', 'desc')->paginate(12);
        
        return view('pages.news.index', compact('pinnedNews', 'allNews'));
    }

    public function show($slug)
    {
        $news = News::where('slug', $slug)->published()->firstOrFail();
        
        // Increment views
        $news->incrementViews();
        
        return view('pages.news.show', compact('news'));
    }

    public function byCategory($category)
    {
        $pinnedNews = collect();
        $allNews = News::published()->byCategory($category)->orderBy('published_at', 'desc')->paginate(12);
        
        return view('pages.news.index', compact('allNews', 'pinnedNews'));
    }
}
