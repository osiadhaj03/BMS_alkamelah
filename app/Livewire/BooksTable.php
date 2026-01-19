<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Book;
use App\Models\BookSection;
use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;

class BooksTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $section = null;
    public $author_id = null;
    public $publisher_id = null;
    public $showSearch = true;
    public $showFilters = true;
    public $title = 'الكتب';
    public $showPagination = true;
    public $showPerPageSelector = true;

    // Advanced filters
    public $sectionFilters = [];
    public $authorFilters = [];
    public $filterModalOpen = false;
    public $filterSearch = '';
    public $activeFilterTab = 'sections';

    // Authors pagination
    public $authorsPage = 1;
    public $authorsPerPage = 30;
    public $loadedAuthors = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'section' => ['except' => null],
    ];

    public function mount($section = null, $author_id = null, $publisher_id = null, $showSearch = true, $showFilters = true, $title = 'الكتب', $perPage = 10, $showPagination = true, $showPerPageSelector = true)
    {
        $this->section = $section;
        $this->author_id = $author_id;
        $this->publisher_id = $publisher_id;
        $this->showSearch = $showSearch;
        $this->showFilters = $showFilters;
        $this->title = $title;
        $this->perPage = $perPage;
        $this->showPagination = $showPagination;
        $this->showPerPageSelector = $showPerPageSelector;

        // إذا تم تمرير section من URL، أضفها للفلاتر
        if ($section) {
            $this->sectionFilters = [(int) $section];
        }

        // قراءة البحث من URL
        if (request()->has('search')) {
            $this->search = request('search');
        }

        // قراءة فلاتر الأقسام من URL
        if (request()->has('sectionFilters')) {
            $filters = request('sectionFilters');
            $this->sectionFilters = is_array($filters) ? array_map('intval', $filters) : [(int) $filters];
        }

        // قراءة فلاتر المؤلفين من URL
        if (request()->has('authorFilters')) {
            $filters = request('authorFilters');
            $this->authorFilters = is_array($filters) ? array_map('intval', $filters) : [(int) $filters];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingSection()
    {
        $this->resetPage();
    }

    public function updatingSectionFilters()
    {
        $this->resetPage();
    }

    public function updatingAuthorFilters()
    {
        $this->resetPage();
    }

    public function toggleSectionFilter($sectionId)
    {
        if (in_array($sectionId, $this->sectionFilters)) {
            $this->sectionFilters = array_values(array_diff($this->sectionFilters, [$sectionId]));
        } else {
            $this->sectionFilters[] = $sectionId;
        }
        $this->resetPage();
    }

    public function toggleAuthorFilter($authorId)
    {
        if (in_array($authorId, $this->authorFilters)) {
            $this->authorFilters = array_values(array_diff($this->authorFilters, [$authorId]));
        } else {
            $this->authorFilters[] = $authorId;
        }
        $this->resetPage();
    }

    public function clearAllFilters()
    {
        $this->sectionFilters = [];
        $this->authorFilters = [];
        $this->resetPage();
    }

    public function getSections()
    {
        $query = BookSection::where('is_active', true);

        if ($this->filterSearch && $this->activeFilterTab === 'sections') {
            $query->where('name', 'like', '%' . $this->filterSearch . '%');
        }

        return $query->orderBy('sort_order')->get();
    }

    public function getAuthors()
    {
        $query = Author::query();

        if ($this->filterSearch && $this->activeFilterTab === 'authors') {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->filterSearch . '%')
                    ->orWhere('last_name', 'like', '%' . $this->filterSearch . '%')
                    ->orWhere('laqab', 'like', '%' . $this->filterSearch . '%');
            });
        }

        return $query->orderBy('first_name')
            ->limit($this->authorsPage * $this->authorsPerPage)
            ->get();
    }

    public function loadMoreAuthors()
    {
        $this->authorsPage++;
    }

    public function resetAuthorsPage()
    {
        $this->authorsPage = 1;
    }

    public function hasMoreAuthors()
    {
        $query = Author::query();

        if ($this->filterSearch && $this->activeFilterTab === 'authors') {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->filterSearch . '%')
                    ->orWhere('last_name', 'like', '%' . $this->filterSearch . '%')
                    ->orWhere('laqab', 'like', '%' . $this->filterSearch . '%');
            });
        }

        $total = $query->count();
        return $total > ($this->authorsPage * $this->authorsPerPage);
    }

    public function updatingFilterSearch()
    {
        $this->authorsPage = 1;
    }

    public function getBooks()
    {
        $query = Book::with(['authors', 'bookSection', 'publisher'])
            ->withCount(['pages', 'volumes', 'chapters']);

        // تطبيق فلاتر الأقسام المتعددة
        if (!empty($this->sectionFilters)) {
            $query->whereIn('book_section_id', $this->sectionFilters);
        }

        // تطبيق فلتر المؤلف إذا كان محدداً (القديم)
        if ($this->author_id) {
            $query->whereHas('authors', function (Builder $authorQuery) {
                $authorQuery->where('authors.id', $this->author_id);
            });
        }

        // تطبيق فلاتر المؤلفين المتعددين (الجديد)
        if (!empty($this->authorFilters)) {
            $query->whereHas('authors', function (Builder $authorQuery) {
                $authorQuery->whereIn('authors.id', $this->authorFilters);
            });
        }

        // تطبيق فلتر الناشر إذا كان محدداً
        if ($this->publisher_id) {
            $query->where('publisher_id', $this->publisher_id);
        }

        // تطبيق البحث
        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('authors', function (Builder $authorQuery) {
                        $authorQuery->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function getCurrentSection()
    {
        if ($this->section) {
            return BookSection::where('id', $this->section)->first();
        }
        return null;
    }

    public function highlightText($text, $search)
    {
        if (empty($search) || empty($text)) {
            return $text;
        }

        return preg_replace(
            '/(' . preg_quote($search, '/') . ')/iu',
            '<mark class="bg-yellow-200 text-yellow-900 px-1 rounded font-medium">$1</mark>',
            $text
        );
    }

    public function getActiveFiltersCount()
    {
        return count($this->sectionFilters) + count($this->authorFilters);
    }

    public function render()
    {
        return view('livewire.books-table', [
            'books' => $this->getBooks(),
            'sections' => $this->getSections(),
            'authors' => $this->getAuthors(),
        ]);
    }
}