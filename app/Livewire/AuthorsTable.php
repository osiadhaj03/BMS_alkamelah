<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;

class AuthorsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showSearch = true;
    public $showFilters = true;
    public $title = 'المؤلفين';
    public $showPagination = true;
    public $showPerPageSelector = true;
    public $madhhab = null;

    // Advanced filters
    public $madhhabFilters = [];
    public $filterModalOpen = false;
    public $filterSearch = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
        'perPage' => ['except' => 10],
        'madhhab' => ['except' => null],
    ];

    // قائمة المذاهب المتاحة
    public $availableMadhhabs = [
        'المذهب الحنفي',
        'المذهب المالكي',
        'المذهب الشافعي',
        'المذهب الحنبلي',
        'أصول الفقه',
        'المذهب الظاهري',
    ];

    public function mount($showSearch = true, $showFilters = true, $title = 'المؤلفين', $perPage = 10, $showPagination = true, $showPerPageSelector = true)
    {
        $this->showSearch = $showSearch;
        $this->showFilters = $showFilters;
        $this->title = $title;
        $this->perPage = $perPage;
        $this->showPagination = $showPagination;
        $this->showPerPageSelector = $showPerPageSelector;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingMadhhab()
    {
        $this->resetPage();
    }

    public function toggleMadhhabFilter($madhhab)
    {
        if (in_array($madhhab, $this->madhhabFilters)) {
            $this->madhhabFilters = array_values(array_diff($this->madhhabFilters, [$madhhab]));
        } else {
            $this->madhhabFilters[] = $madhhab;
        }
        $this->resetPage();
    }

    public function clearAllFilters()
    {
        $this->madhhabFilters = [];
        $this->resetPage();
    }

    public function getActiveFiltersCount()
    {
        return count($this->madhhabFilters);
    }

    public function getFilteredMadhhabs()
    {
        if (!$this->filterSearch) {
            return $this->availableMadhhabs;
        }

        return array_filter($this->availableMadhhabs, function ($m) {
            return str_contains($m, $this->filterSearch);
        });
    }

    public function getAuthors()
    {
        $query = Author::withCount('books');

        // تطبيق فلترة المذهب (القديم)
        if ($this->madhhab) {
            $query->where('madhhab', $this->madhhab);
        }

        // تطبيق فلاتر المذاهب المتعددة (الجديد)
        if (!empty($this->madhhabFilters)) {
            $query->whereIn('madhhab', $this->madhhabFilters);
        }

        // تطبيق البحث
        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('laqab', 'like', '%' . $this->search . '%')
                    ->orWhere('biography', 'like', '%' . $this->search . '%')
                    ->orWhere('madhhab', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('books_count', 'desc')
            ->orderBy('first_name', 'asc')
            ->paginate($this->perPage);
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

    public function render()
    {
        return view('livewire.authors-table', [
            'authors' => $this->getAuthors(),
        ]);
    }
}