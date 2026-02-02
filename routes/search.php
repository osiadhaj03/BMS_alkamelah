<?php

use Illuminate\Support\Facades\Route;
use App\Services\UltraFastSearchService;

Route::get('/api/search', function () {
    $query = request()->input('q', '');
    $page = request()->input('page', 1);
    $size = request()->input('size', 10);
    
    if (empty($query)) {
        return response()->json([
            'error' => 'Search query is required',
            'status' => 'error'
        ]);
    }
    
    try {
        $searchService = new UltraFastSearchService();
        $results = $searchService->search($query, [], $page, $size);
        
        return response()->json($results);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'status' => 'error'
        ], 500);
    }
});
