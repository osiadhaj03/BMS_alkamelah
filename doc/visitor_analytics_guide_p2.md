# 📊 دليل تنفيذ تحسين نظام تتبع الزيارات — المراحل 3-4

> **المتطلب:** إكمال المراحل 1-2 من `visitor_analytics_guide_p1.md`

---

## 🔷 المرحلة 3A: تحديد الدولة والمدينة (GeoIP)

### الخطوة 1: تثبيت Package + قاعدة البيانات

```bash
composer require geoip2/geoip2
```

ثم:

1. سجل حساب مجاني في [MaxMind](https://www.maxmind.com/en/geolite2/signup)
2. نزّل ملف `GeoLite2-City.mmdb` (~70MB)
3. ضعه في: `storage/app/geoip/GeoLite2-City.mmdb`

### الخطوة 2: Migration

**الأمر:** `php artisan make:migration add_geo_columns_to_page_visits`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->string('country', 100)->nullable()->after('os');
            $table->string('city', 100)->nullable()->after('country');
            $table->string('country_code', 2)->nullable()->after('city');

            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropColumn(['country', 'city', 'country_code']);
        });
    }
};
```

### الخطوة 3: GeoIPService

**الملف:** `app/Services/GeoIPService.php`

```php
<?php

namespace App\Services;

use GeoIp2\Database\Reader;

class GeoIPService
{
    protected ?Reader $reader = null;

    public function __construct()
    {
        $dbPath = storage_path('app/geoip/GeoLite2-City.mmdb');
        if (file_exists($dbPath)) {
            $this->reader = new Reader($dbPath);
        }
    }

    /**
     * البحث عن موقع IP
     * @return array{country: ?string, city: ?string, country_code: ?string}
     */
    public function lookup(string $ip): array
    {
        if (!$this->reader || $this->isPrivateIP($ip)) {
            return ['country' => null, 'city' => null, 'country_code' => null];
        }

        try {
            $record = $this->reader->city($ip);
            return [
                'country'      => $record->country->name ?? null,
                'city'         => $record->city->name ?? null,
                'country_code' => $record->country->isoCode ?? null,
            ];
        } catch (\Exception $e) {
            return ['country' => null, 'city' => null, 'country_code' => null];
        }
    }

    protected function isPrivateIP(string $ip): bool
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
```

### الخطوة 4: تعديل Middleware

**في `TrackPageVisits.php`** — أضف بعد `UserAgentParser::parse()`:

```diff
+ $geoService = new \App\Services\GeoIPService();
+ $geoData = $geoService->lookup($request->ip());

  $visit = PageVisit::create([
      // ... الحقول الموجودة ...
+     'country'      => $geoData['country'],
+     'city'         => $geoData['city'],
+     'country_code' => $geoData['country_code'],
      'visited_at'   => now(),
  ]);
```

**وأضف الحقول الجديدة لـ `$fillable` في `PageVisit` model.**

### الخطوة 5: VisitorCountriesChart

**الملف:** `app/Filament/Widgets/VisitorCountriesChart.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class VisitorCountriesChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع الدول (Top 10)';
    protected static ?int $sort = 24;

    protected function getData(): array
    {
        $countries = PageVisit::humans()
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'country')
            ->toArray();

        $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6',
                    '#06b6d4','#ec4899','#84cc16','#f97316','#6366f1'];

        return [
            'datasets' => [[
                'data' => array_values($countries),
                'backgroundColor' => array_slice($colors, 0, count($countries)),
            ]],
            'labels' => array_keys($countries),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
```

---

## 🔷 المرحلة 3B: رحلة الزائر (Visitor Journey)

### صفحة Filament

**الملف:** `app/Filament/Resources/PageVisitResource/Pages/ViewVisitorJourney.php`

```php
<?php

namespace App\Filament\Resources\PageVisitResource\Pages;

use App\Filament\Resources\PageVisitResource;
use App\Models\PageVisit;
use Filament\Resources\Pages\Page;

class ViewVisitorJourney extends Page
{
    protected static string $resource = PageVisitResource::class;
    protected static string $view = 'filament.pages.visitor-journey';
    protected static ?string $title = 'رحلة الزائر';

    public string $sessionId = '';
    public $visits = [];

    public function mount(): void
    {
        $this->sessionId = request('session');

        $this->visits = PageVisit::where('session_id', $this->sessionId)
            ->orderBy('visited_at')
            ->get();
    }
}
```

### Blade Template

**الملف:** `resources/views/filament/pages/visitor-journey.blade.php`

```blade
<x-filament-panels::page>
    <div class="space-y-4">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow">
            <h3 class="text-lg font-bold mb-2">
                الجلسة: {{ Str::limit($sessionId, 20) }}
            </h3>
            <p class="text-sm text-gray-500">
                IP: {{ $visits->first()?->ip_address ?? '-' }}
                | عدد الصفحات: {{ $visits->count() }}
                | إجمالي الوقت: {{ App\Models\PageVisit::formatDuration($visits->sum('duration_seconds')) }}
            </p>
        </div>

        <div class="relative pr-8">
            @foreach($visits as $index => $visit)
                <div class="flex items-start gap-4 mb-6 relative">
                    {{-- الخط العمودي --}}
                    @if(!$loop->last)
                        <div class="absolute right-3 top-10 w-0.5 h-full bg-gray-300 dark:bg-gray-600"></div>
                    @endif

                    {{-- النقطة --}}
                    <div class="relative z-10 w-7 h-7 rounded-full flex items-center justify-center text-white text-xs
                        {{ $loop->first ? 'bg-green-500' : ($loop->last ? 'bg-red-500' : 'bg-blue-500') }}">
                        {{ $index + 1 }}
                    </div>

                    {{-- المحتوى --}}
                    <div class="flex-1 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold">
                                    {{ $visit->page_title ?? $visit->route_name ?? 'صفحة غير معروفة' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1 truncate max-w-md" dir="ltr">
                                    {{ $visit->url }}
                                </p>
                            </div>
                            <div class="text-left text-sm">
                                <span class="text-gray-500">{{ $visit->visited_at?->format('H:i:s') }}</span>
                                @if($visit->duration_seconds)
                                    <br>
                                    <span class="text-blue-500 font-medium">
                                        {{ App\Models\PageVisit::formatDuration($visit->duration_seconds) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
```

### تسجيل الصفحة في Resource

**أضف في `PageVisitResource::getPages()`:**

```diff
  public static function getPages(): array
  {
      return [
          'index'   => Pages\ListPageVisits::route('/'),
+         'journey' => Pages\ViewVisitorJourney::route('/journey'),
      ];
  }
```

**وأضف Action في الجدول للزر:**

```php
// في table() → actions([])
\Filament\Actions\Action::make('journey')
    ->label('رحلة الزائر')
    ->icon('heroicon-o-arrow-trending-up')
    ->url(fn ($record) => static::getUrl('journey', ['session' => $record->session_id]))
    ->visible(fn ($record) => $record->session_id !== null),
```

---

## 🔷 المرحلة 3C: تحليل المصادر (Referrer Analytics)

### Migration

```php
Schema::table('page_visits', function (Blueprint $table) {
    $table->string('source_type', 50)->nullable()->after('country_code');
    // القيم: google, facebook, twitter, direct, internal, other
    $table->index('source_type');
});
```

### ReferrerParser Service

**الملف:** `app/Services/ReferrerParser.php`

```php
<?php

namespace App\Services;

class ReferrerParser
{
    protected static array $sources = [
        'google'    => ['google.com', 'google.co', 'googleapis.com'],
        'bing'      => ['bing.com'],
        'yahoo'     => ['yahoo.com'],
        'facebook'  => ['facebook.com', 'fb.com', 'fbcdn.net'],
        'twitter'   => ['twitter.com', 't.co', 'x.com'],
        'youtube'   => ['youtube.com', 'youtu.be'],
        'linkedin'  => ['linkedin.com'],
        'whatsapp'  => ['whatsapp.com', 'wa.me'],
        'telegram'  => ['telegram.org', 't.me'],
    ];

    public static function parse(?string $referer, ?string $siteHost = null): string
    {
        if (empty($referer)) return 'direct';

        $host = strtolower(parse_url($referer, PHP_URL_HOST) ?? '');

        // رابط داخلي
        if ($siteHost && str_contains($host, strtolower($siteHost))) {
            return 'internal';
        }

        foreach (static::$sources as $name => $domains) {
            foreach ($domains as $domain) {
                if (str_contains($host, $domain)) return $name;
            }
        }

        return 'other';
    }
}
```

### TrafficSourcesChart Widget

**الملف:** `app/Filament/Widgets/TrafficSourcesChart.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class TrafficSourcesChart extends ChartWidget
{
    protected static ?string $heading = 'مصادر الزيارات';
    protected static ?int $sort = 25;

    protected function getData(): array
    {
        $sources = PageVisit::humans()
            ->whereNotNull('source_type')
            ->selectRaw('source_type, COUNT(*) as count')
            ->groupBy('source_type')
            ->orderByDesc('count')
            ->pluck('count', 'source_type')
            ->toArray();

        $labels = array_map(fn ($s) => match($s) {
            'google' => '🔍 Google', 'direct' => '🔗 مباشر',
            'facebook' => '📘 Facebook', 'twitter' => '🐦 Twitter',
            'internal' => '🏠 داخلي', default => ucfirst($s),
        }, array_keys($sources));

        return [
            'datasets' => [[
                'data' => array_values($sources),
                'backgroundColor' => ['#4285f4','#34a853','#1877f2','#1da1f2','#6366f1','#f59e0b','#ef4444','#8b5cf6','#06b6d4'],
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string { return 'doughnut'; }
}
```

---

## 🔷 المرحلة 4A: لوحة Real-time

**الملف:** `app/Filament/Widgets/RealTimeVisitorsWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\Widget;

class RealTimeVisitorsWidget extends Widget
{
    protected static string $view = 'filament.widgets.real-time-visitors';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    // تحديث كل 10 ثواني
    protected static ?string $pollingInterval = '10s';

    public function getVisitorsData(): array
    {
        $cutoff = now()->subMinutes(5);

        $activeVisitors = PageVisit::humans()
            ->where('visited_at', '>=', $cutoff)
            ->distinct('ip_address')
            ->count('ip_address');

        $activePages = PageVisit::humans()
            ->where('visited_at', '>=', $cutoff)
            ->selectRaw('page_title, route_name, COUNT(DISTINCT ip_address) as visitors')
            ->groupBy('page_title', 'route_name')
            ->orderByDesc('visitors')
            ->limit(10)
            ->get();

        return [
            'count' => $activeVisitors,
            'pages' => $activePages,
        ];
    }
}
```

**القالب:** `resources/views/filament/widgets/real-time-visitors.blade.php`

```blade
<x-filament-widgets::widget>
    @php $data = $this->getVisitorsData(); @endphp
    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
            <h3 class="text-lg font-bold">الآن على الموقع: {{ $data['count'] }} زائر</h3>
        </div>
        @if($data['pages']->count())
            <div class="space-y-2">
                @foreach($data['pages'] as $page)
                    <div class="flex justify-between items-center py-1 px-3 rounded bg-gray-50 dark:bg-gray-700">
                        <span>{{ $page->page_title ?? $page->route_name ?? '/' }}</span>
                        <span class="text-sm font-semibold text-blue-500">{{ $page->visitors }} زائر</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
```

---

## 🔷 المرحلة 4B: تقارير تلقائية بالبريد

### Mailable

**الأمر:** `php artisan make:mail DailyVisitsReport`

**الملف:** `app/Mail/DailyVisitsReport.php`

```php
<?php

namespace App\Mail;

use App\Models\PageVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyVisitsReport extends Mailable
{
    use Queueable, SerializesModels;

    public array $stats;

    public function __construct()
    {
        $this->stats = [
            'total'    => PageVisit::humans()->whereDate('visited_at', yesterday())->count(),
            'unique'   => PageVisit::humans()->whereDate('visited_at', yesterday())
                              ->distinct('ip_address')->count('ip_address'),
            'top_pages' => PageVisit::humans()->whereDate('visited_at', yesterday())
                              ->selectRaw('page_title, COUNT(*) as cnt')
                              ->groupBy('page_title')->orderByDesc('cnt')->limit(5)->get(),
            'date'     => yesterday()->format('Y-m-d'),
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "📊 تقرير زيارات {$this->stats['date']}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.daily-visits-report');
    }
}
```

### Email Template

**الملف:** `resources/views/emails/daily-visits-report.blade.php`

```blade
<h2>📊 تقرير زيارات يوم {{ $stats['date'] }}</h2>

<table style="border-collapse:collapse; width:100%; margin:20px 0">
    <tr>
        <td style="padding:12px; background:#3b82f6; color:white; font-weight:bold">إجمالي الزيارات</td>
        <td style="padding:12px; font-size:24px; font-weight:bold">{{ number_format($stats['total']) }}</td>
    </tr>
    <tr>
        <td style="padding:12px; background:#10b981; color:white; font-weight:bold">زوار فريدين</td>
        <td style="padding:12px; font-size:24px; font-weight:bold">{{ number_format($stats['unique']) }}</td>
    </tr>
</table>

<h3>أكثر الصفحات زيارة:</h3>
<ol>
    @foreach($stats['top_pages'] as $page)
        <li>{{ $page->page_title ?? 'غير معروف' }} — <strong>{{ $page->cnt }}</strong> زيارة</li>
    @endforeach
</ol>
```

### Artisan Command

**الملف:** `app/Console/Commands/SendVisitsReport.php`

```php
<?php

namespace App\Console\Commands;

use App\Mail\DailyVisitsReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendVisitsReport extends Command
{
    protected $signature = 'visits:report {--email= : البريد المستلم}';
    protected $description = 'إرسال تقرير الزيارات اليومي بالبريد';

    public function handle(): void
    {
        $email = $this->option('email') ?? config('mail.admin_email', 'admin@example.com');
        Mail::to($email)->send(new DailyVisitsReport());
        $this->info("✅ تم إرسال التقرير إلى: {$email}");
    }
}
```

### الجدولة

```php
// في routes/console.php أو Kernel.php
Schedule::command('visits:report --email=admin@example.com')->dailyAt('07:00');
```

---

## 📋 ملخص ملفات المراحل 3-4

| # | الملف | الحالة | المرحلة |
|---|-------|--------|---------|
| 1 | Migration: geo columns | 🆕 | 3A |
| 2 | `GeoIPService.php` | 🆕 | 3A |
| 3 | `VisitorCountriesChart.php` | 🆕 | 3A |
| 4 | `ViewVisitorJourney.php` | 🆕 | 3B |
| 5 | `visitor-journey.blade.php` | 🆕 | 3B |
| 6 | Migration: source_type | 🆕 | 3C |
| 7 | `ReferrerParser.php` | 🆕 | 3C |
| 8 | `TrafficSourcesChart.php` | 🆕 | 3C |
| 9 | `RealTimeVisitorsWidget.php` | 🆕 | 4A |
| 10 | `real-time-visitors.blade.php` | 🆕 | 4A |
| 11 | `DailyVisitsReport.php` | 🆕 | 4B |
| 12 | `daily-visits-report.blade.php` | 🆕 | 4B |
| 13 | `SendVisitsReport.php` | 🆕 | 4B |
| 14 | `TrackPageVisits.php` | ✏️ تعديل | 3A+3C |
| 15 | `PageVisit.php` model | ✏️ تعديل | 3A+3C |

> [!TIP]
> **ترتيب التنفيذ المقترح:** 3A (GeoIP) → 3C (المصادر) → 3B (رحلة الزائر) → 4A (Real-time) → 4B (تقارير)
> لأن 3A و3C يعدّلان الـ Middleware معاً، فالأفضل تنفيذهما متتاليين.
