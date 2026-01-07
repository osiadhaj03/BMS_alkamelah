<!-- Authors Section -->
<div class="bg-gray-50 py-16" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-green-800 mb-8 text-center">المؤلفين</h2>

        <!-- Authors Table -->
        @livewire('authors-table', ['showSearch' => false, 'showFilters' => true, 'perPage' => 10])
    </div>
</div>