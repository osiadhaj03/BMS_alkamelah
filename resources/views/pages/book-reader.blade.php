<x-superduper.main title="{{ $book->title }} - قارئ الكتب">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <x-layout.book-reader-header :book="$book" :currentPage="$currentPage" :totalPages="$totalPages" />
        
        <!-- Main Content -->
        <div class="flex h-screen pt-20">
            <!-- Left Sidebar - Table of Contents -->
            <x-layout.table-of-contents :sections="$sections" :book="$book" :currentPage="$currentPage" />
            
            <!-- Center - Book Content -->
            <x-layout.book-content 
                :book="$book" 
                :content="$bookContent" 
                :currentPage="$currentPage" 
                :totalPages="$totalPages"
                :previousPage="$previousPage"
                :nextPage="$nextPage" 
            />
            
            <!-- Right Sidebar - Chapter Navigation -->
            <x-layout.chapter-navigation :sections="$sections" :book="$book" :currentPage="$currentPage" />
        </div>
    </div>
</x-superduper.main>