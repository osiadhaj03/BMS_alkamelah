<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'parent_id' => 'nullable|exists:article_comments,id',
        ], [
            'comment.required' => 'التعليق مطلوب',
            'comment.max' => 'التعليق يجب ألا يتجاوز 1000 حرف',
            'email.email' => 'البريد الإلكتروني غير صحيح',
        ]);

        $comment = new ArticleComment($validated);
        $comment->article_id = $article->id;
        
        if (auth()->check()) {
            $comment->user_id = auth()->id();
        }
        
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة تعليقك بنجاح. سيتم نشره بعد المراجعة.',
        ]);
    }
}
