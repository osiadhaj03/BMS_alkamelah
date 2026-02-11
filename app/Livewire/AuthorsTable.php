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
    public $activeFilterTab = 'madhhab';

    // Century and date range filters
    public $centuryFilters = [];
    public $deathDateFrom = null;
    public $deathDateTo = null;

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
    ];

    // قائمة القرون الهجرية (من 1 إلى 15)
    public $availableCenturies = [
        1 => 'القرن الأول',
        2 => 'القرن الثاني',
        3 => 'القرن الثالث',
        4 => 'القرن الرابع',
        5 => 'القرن الخامس',
        6 => 'القرن السادس',
        7 => 'القرن السابع',
        8 => 'القرن الثامن',
        9 => 'القرن التاسع',
        10 => 'القرن العاشر',
        11 => 'القرن الحادي عشر',
        12 => 'القرن الثاني عشر',
        13 => 'القرن الثالث عشر',
        14 => 'القرن الرابع عشر',
        15 => 'القرن الخامس عشر',
    ];

    public function mount($showSearch = true, $showFilters = true, $title = 'المؤلفين', $perPage = 10, $showPagination = true, $showPerPageSelector = true)
    {
        $this->showSearch = $showSearch;
        $this->showFilters = $showFilters;
        $this->title = $title;
        $this->perPage = $perPage;
        $this->showPagination = $showPagination;
        $this->showPerPageSelector = $showPerPageSelector;

        // قراءة البحث من URL
        if (request()->has('search')) {
            $this->search = request('search');
        }

        // قراءة فلاتر المذاهب من URL
        if (request()->has('madhhabFilters')) {
            $filters = request('madhhabFilters');
            $this->madhhabFilters = is_array($filters) ? $filters : [$filters];
        }

        // قراءة فلاتر القرون من URL
        if (request()->has('centuryFilters')) {
            $filters = request('centuryFilters');
            $this->centuryFilters = is_array($filters) ? array_map('intval', $filters) : [(int) $filters];
        }

        // قراءة نطاق التاريخ من URL
        if (request()->has('deathDateFrom')) {
            $this->deathDateFrom = request('deathDateFrom');
        }
        if (request()->has('deathDateTo')) {
            $this->deathDateTo = request('deathDateTo');
        }
    }

    // تتبع آخر بحث مسجل لمنع التكرار
    protected static string $lastLoggedAuthorSearch = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch($value)
    {
        if (!empty($value) && $value !== static::$lastLoggedAuthorSearch) {
            static::$lastLoggedAuthorSearch = $value;
            $this->logAuthorSearch($value);
        }
    }

    protected function logAuthorSearch(string $query): void
    {
        try {
            $ip = request()->ip();
            $lastVisit = \App\Models\PageVisit::where('ip_address', $ip)
                ->latest('visited_at')
                ->first();

            $resultsCount = Author::where(function ($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                    ->orWhere('last_name', 'like', '%' . $query . '%')
                    ->orWhere('laqab', 'like', '%' . $query . '%');
            })->count();

            \App\Models\SearchLog::create([
                'query'         => $query,
                'search_type'   => 'authors',
                'results_count' => $resultsCount,
                'page_visit_id' => $lastVisit?->id,
                'ip_address'    => $ip,
                'filters'       => !empty($this->madhhabFilters) || !empty($this->centuryFilters)
                    ? ['madhhab' => $this->madhhabFilters, 'centuries' => $this->centuryFilters]
                    : null,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SearchLog error (authors-table): ' . $e->getMessage());
        }
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
        $this->centuryFilters = [];
        $this->deathDateFrom = null;
        $this->deathDateTo = null;
        $this->resetPage();
    }

    public function getActiveFiltersCount()
    {
        $count = count($this->madhhabFilters) + count($this->centuryFilters);
        if ($this->deathDateFrom || $this->deathDateTo) {
            $count++;
        }
        return $count;
    }

    public function toggleCenturyFilter($century)
    {
        $century = (int) $century;
        if (in_array($century, $this->centuryFilters)) {
            $this->centuryFilters = array_values(array_diff($this->centuryFilters, [$century]));
        } else {
            $this->centuryFilters[] = $century;
        }
        $this->resetPage();
    }

    public function applyDateRange()
    {
        $this->resetPage();
    }

    public function clearDateRange()
    {
        $this->deathDateFrom = null;
        $this->deathDateTo = null;
        $this->resetPage();
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

        // تطبيق فلاتر القرون الهجرية
        if (!empty($this->centuryFilters)) {
            $query->where(function ($q) {
                foreach ($this->centuryFilters as $century) {
                    $startYear = ($century - 1) * 100 + 1; // بداية القرن
                    $endYear = $century * 100;            // نهاية القرن
                    $q->orWhere(function ($subQ) use ($startYear, $endYear) {
                        $subQ->whereNotNull('death_date')
                            ->whereRaw('YEAR(death_date) >= ?', [$startYear])
                            ->whereRaw('YEAR(death_date) <= ?', [$endYear]);
                    });
                }
            });
        }

        // تطبيق فلتر نطاق تاريخ الوفاة
        if ($this->deathDateFrom) {
            $query->whereNotNull('death_date')
                ->whereRaw('YEAR(death_date) >= ?', [$this->deathDateFrom]);
        }
        if ($this->deathDateTo) {
            $query->whereNotNull('death_date')
                ->whereRaw('YEAR(death_date) <= ?', [$this->deathDateTo]);
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