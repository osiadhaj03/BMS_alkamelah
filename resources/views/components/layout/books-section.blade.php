<!-- Books Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" dir="rtl">
    <h2 class="text-3xl font-bold text-green-800 mb-8 text-center">الكتب</h2>
    
    <!-- Books Table -->
    @livewire('books-table', ['showSearch' => false, 'showFilters' => true, 'perPage' => 10])
</div>