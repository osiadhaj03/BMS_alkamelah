<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    /**
     * Store a new chapter
     */
    public function store(Request $request, $bookId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:chapters,id',
            'after_chapter_id' => 'nullable|exists:chapters,id',
            'page_start' => 'nullable|integer',
            'page_end' => 'nullable|integer',
        ]);

        // Calculate level
        $level = 1;
        $parentId = $request->parent_id;
        
        if ($parentId) {
            $parent = Chapter::find($parentId);
            $level = $parent->level + 1;
        }

        // Calculate order
        $order = 1;
        if ($request->after_chapter_id) {
            $afterChapter = Chapter::find($request->after_chapter_id);
            $order = $afterChapter->order + 1;
            
            // Update order of subsequent chapters
            Chapter::where('book_id', $bookId)
                ->where('parent_id', $parentId)
                ->where('order', '>=', $order)
                ->increment('order');
        } else {
            // Get last order in this level
            $maxOrder = Chapter::where('book_id', $bookId)
                ->where('parent_id', $parentId)
                ->max('order');
            $order = $maxOrder ? $maxOrder + 1 : 1;
        }

        $chapter = Chapter::create([
            'book_id' => $bookId,
            'title' => $request->title,
            'parent_id' => $parentId,
            'level' => $level,
            'order' => $order,
            'page_start' => $request->page_start,
            'page_end' => $request->page_end,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الفصل بنجاح',
            'chapter' => $chapter->load('children')
        ]);
    }

    /**
     * Update a chapter
     */
    public function update(Request $request, $bookId, $chapterId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'page_start' => 'nullable|integer',
            'page_end' => 'nullable|integer',
        ]);

        $chapter = Chapter::where('book_id', $bookId)
            ->where('id', $chapterId)
            ->firstOrFail();

        $chapter->update([
            'title' => $request->title,
            'page_start' => $request->page_start,
            'page_end' => $request->page_end,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الفصل بنجاح',
            'chapter' => $chapter
        ]);
    }

    /**
     * Delete a chapter
     */
    public function delete($bookId, $chapterId)
    {
        $chapter = Chapter::where('book_id', $bookId)
            ->where('id', $chapterId)
            ->firstOrFail();

        $parentId = $chapter->parent_id;
        $order = $chapter->order;

        // Delete chapter and all children
        $this->deleteChapterAndChildren($chapter);

        // Reorder remaining chapters
        Chapter::where('book_id', $bookId)
            ->where('parent_id', $parentId)
            ->where('order', '>', $order)
            ->decrement('order');

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الفصل بنجاح'
        ]);
    }

    /**
     * Reorder a chapter (move up/down or change parent)
     */
    public function reorder(Request $request, $bookId, $chapterId)
    {
        $request->validate([
            'new_order' => 'required|integer|min:1',
            'new_parent_id' => 'nullable|exists:chapters,id',
        ]);

        $chapter = Chapter::where('book_id', $bookId)
            ->where('id', $chapterId)
            ->firstOrFail();

        $oldOrder = $chapter->order;
        $oldParentId = $chapter->parent_id;
        $newOrder = $request->new_order;
        $newParentId = $request->new_parent_id;

        // Update level if parent changed
        if ($oldParentId !== $newParentId) {
            $newLevel = 1;
            if ($newParentId) {
                $newParent = Chapter::find($newParentId);
                $newLevel = $newParent->level + 1;
            }
            $chapter->level = $newLevel;
            $chapter->parent_id = $newParentId;
        }

        // Update order
        if ($oldOrder !== $newOrder || $oldParentId !== $newParentId) {
            // Remove from old position
            Chapter::where('book_id', $bookId)
                ->where('parent_id', $oldParentId)
                ->where('order', '>', $oldOrder)
                ->decrement('order');

            // Insert at new position
            Chapter::where('book_id', $bookId)
                ->where('parent_id', $newParentId)
                ->where('order', '>=', $newOrder)
                ->increment('order');

            $chapter->order = $newOrder;
        }

        $chapter->save();

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة ترتيب الفصل بنجاح',
            'chapter' => $chapter
        ]);
    }

    /**
     * Move chapter up
     */
    public function moveUp($bookId, $chapterId)
    {
        $chapter = Chapter::where('book_id', $bookId)
            ->where('id', $chapterId)
            ->firstOrFail();

        if ($chapter->order <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'الفصل في أول الترتيب'
            ], 400);
        }

        // Find previous chapter
        $previousChapter = Chapter::where('book_id', $bookId)
            ->where('parent_id', $chapter->parent_id)
            ->where('order', $chapter->order - 1)
            ->first();

        if ($previousChapter) {
            // Swap orders
            $tempOrder = $chapter->order;
            $chapter->order = $previousChapter->order;
            $previousChapter->order = $tempOrder;

            $chapter->save();
            $previousChapter->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحريك الفصل للأعلى'
        ]);
    }

    /**
     * Move chapter down
     */
    public function moveDown($bookId, $chapterId)
    {
        $chapter = Chapter::where('book_id', $bookId)
            ->where('id', $chapterId)
            ->firstOrFail();

        // Find next chapter
        $nextChapter = Chapter::where('book_id', $bookId)
            ->where('parent_id', $chapter->parent_id)
            ->where('order', $chapter->order + 1)
            ->first();

        if (!$nextChapter) {
            return response()->json([
                'success' => false,
                'message' => 'الفصل في آخر الترتيب'
            ], 400);
        }

        // Swap orders
        $tempOrder = $chapter->order;
        $chapter->order = $nextChapter->order;
        $nextChapter->order = $tempOrder;

        $chapter->save();
        $nextChapter->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحريك الفصل للأسفل'
        ]);
    }

    /**
     * Recursively delete chapter and all children
     */
    private function deleteChapterAndChildren($chapter)
    {
        // Delete all children first
        foreach ($chapter->children as $child) {
            $this->deleteChapterAndChildren($child);
        }
        
        // Delete the chapter itself
        $chapter->delete();
    }
}
