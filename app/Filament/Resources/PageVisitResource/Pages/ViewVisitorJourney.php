<?php

namespace App\Filament\Resources\PageVisitResource\Pages;

use App\Filament\Resources\PageVisitResource;
use App\Models\PageVisit;
use Filament\Resources\Pages\Page;

class ViewVisitorJourney extends Page
{
    protected static string $resource = PageVisitResource::class;
    protected string $view = 'filament.pages.visitor-journey';
    protected ?string $title = 'رحلة الزائر';

    public string $sessionId = '';
    public $visits = [];

    public function mount(): void
    {
        $this->sessionId = request('session') ?? '';

        $this->visits = PageVisit::where('session_id', $this->sessionId)
            ->orderBy('visited_at')
            ->get();
    }
}
