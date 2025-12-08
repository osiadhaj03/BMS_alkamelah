{{-- Navigation Bar - شريط التنقل السفلي --}}

<nav class="reader-nav-bar">
    {{-- الجانب الأيمن: أزرار التنقل --}}
    <div class="nav-controls">
        {{-- السابق --}}
        <button 
            class="nav-btn nav-prev {{ !$this->hasPreviousPage ? 'disabled' : '' }}"
            wire:click="previousPage"
            @if(!$this->hasPreviousPage) disabled @endif
            title="الصفحة السابقة (→)"
        >
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </button>
        
        {{-- إدخال رقم الصفحة --}}
        <div class="nav-page-input">
            <input 
                type="number"
                min="1"
                max="{{ $this->totalPages }}"
                wire:model.blur="pageNumber"
                wire:change="goToPage($event.target.value)"
                class="page-input"
                title="اذهب للصفحة"
            >
            <span class="page-total-label">/ {{ $this->totalPages }}</span>
        </div>
        
        {{-- التالي --}}
        <button 
            class="nav-btn nav-next {{ !$this->hasNextPage ? 'disabled' : '' }}"
            wire:click="nextPage"
            @if(!$this->hasNextPage) disabled @endif
            title="الصفحة التالية (←)"
        >
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
            </svg>
        </button>
    </div>
    
    {{-- الوسط: شريط التقدم --}}
    <div class="nav-progress">
        <div class="progress-container">
            {{-- نسبة التقدم --}}
            <span class="progress-percentage">{{ $this->progressPercentage }}%</span>
            
            {{-- شريط التقدم --}}
            <div class="progress-track">
                <div class="progress-bar-fill" style="width: {{ $this->progressPercentage }}%"></div>
                <input 
                    type="range"
                    min="1"
                    max="{{ $this->totalPages }}"
                    value="{{ $pageNumber }}"
                    class="progress-slider"
                    @input="$wire.goToPage($event.target.value)"
                    title="اسحب للتنقل"
                >
            </div>
        </div>
    </div>
    
    {{-- الجانب الأيسر: اختيار الجزء --}}
    @if($this->volumes->isNotEmpty())
        <div class="nav-volume">
            <label class="volume-label">الجزء:</label>
            <select 
                class="volume-select"
                wire:model="volumeId"
                wire:change="goToVolume($event.target.value)"
            >
                @foreach($this->volumes as $volume)
                    <option value="{{ $volume->id }}">
                        {{ $volume->title ?: 'الجزء ' . $volume->number }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</nav>
