# 📊 دليل تنفيذ تحسين نظام تتبع الزيارات — المراحل 1-2

> **المتطلب:** النظام الأساسي يعمل (`page_visits` + Middleware + Filament Resource)
> **هذا الدليل يغطي:** Charts + كشف الأجهزة + تحسين الأداء

---

## 🔷 المرحلة 1A: الرسوم البيانية (Charts)

### الملفات المطلوبة

| # | الملف | الوصف |
|---|-------|-------|
| 1 | `app/Filament/Widgets/VisitsLineChart.php` | خط بياني — زيارات آخر 30 يوم |
| 2 | `app/Filament/Widgets/DeviceDistributionChart.php` | دائري — توزيع الأجهزة |
| 3 | `app/Filament/Widgets/TopPagesChart.php` | أعمدة — أكثر 10 صفحات زيارة |
| 4 | `app/Filament/Widgets/HourlyVisitsChart.php` | أعمدة — الزيارات حسب الساعة |

---

### 1. VisitsLineChart.php — زيارات آخر 30 يوم

```php
<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VisitsLineChart extends ChartWidget
{
    protected static ?string $heading = 'الزيارات اليومية (آخر 30 يوم)';
    protected static ?int $sort = 20;
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $visits = PageVisit::humans()
            ->where('visited_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(visited_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'الزيارات',
                    'data' => $days->map(fn ($d) => $visits[$d->format('Y-m-d')] ?? 0)->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $days->map(fn ($d) => $d->format('m/d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
```

---

### 2. DeviceDistributionChart.php — توزيع الأجهزة

> [!IMPORTANT]
> هذا الـ Chart يعتمد على عمود `device_type` الذي سيُضاف في **المرحلة 1B** (كشف الأجهزة).
> حتى يُضاف العمود، يمكن استخدام تحليل `user_agent` مؤقتاً.

```php
<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class DeviceDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع الأجهزة';
    protected static ?int $sort = 21;
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        // تحليل مؤقت من user_agent (قبل إضافة عمود device_type)
        $total = PageVisit::humans()->count() ?: 1;
        $mobile = PageVisit::humans()
            ->where(function ($q) {
                $q->where('user_agent', 'like', '%Mobile%')
                  ->orWhere('user_agent', 'like', '%Android%')
                  ->orWhere('user_agent', 'like', '%iPhone%');
            })->count();
        $tablet = PageVisit::humans()
            ->where(function ($q) {
                $q->where('user_agent', 'like', '%iPad%')
                  ->orWhere('user_agent', 'like', '%Tablet%');
            })->count();
        $desktop = $total - $mobile - $tablet;

        // بعد إضافة عمود device_type، استبدل بـ:
        // $counts = PageVisit::humans()
        //     ->selectRaw("device_type, COUNT(*) as count")
        //     ->groupBy('device_type')
        //     ->pluck('count', 'device_type');

        return [
            'datasets' => [[
                'data' => [$desktop, $mobile, $tablet],
                'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b'],
            ]],
            'labels' => ['🖥️ Desktop', '📱 Mobile', '📟 Tablet'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
```

---

### 3. TopPagesChart.php — أكثر 10 صفحات زيارة

```php
<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class TopPagesChart extends ChartWidget
{
    protected static ?string $heading = 'أكثر الصفحات زيارة (Top 10)';
    protected static ?int $sort = 22;
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $topPages = PageVisit::topPages(10);

        return [
            'datasets' => [[
                'label' => 'عدد الزيارات',
                'data' => $topPages->pluck('visits_count')->toArray(),
                'backgroundColor' => [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                    '#06b6d4', '#ec4899', '#84cc16', '#f97316', '#6366f1',
                ],
            ]],
            'labels' => $topPages->map(fn ($p) => $p->page_title ?? $p->route_name ?? 'غير معروف')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // أعمدة أفقية
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}
```

---

### 4. HourlyVisitsChart.php — الزيارات حسب الساعة

```php
<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;

class HourlyVisitsChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع الزيارات حسب الساعة (اليوم)';
    protected static ?int $sort = 23;
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $hourly = PageVisit::humans()
            ->whereDate('visited_at', today())
            ->selectRaw('HOUR(visited_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $data = collect(range(0, 23))->map(fn ($h) => $hourly[$h] ?? 0)->toArray();
        $labels = collect(range(0, 23))->map(fn ($h) => sprintf('%02d:00', $h))->toArray();

        return [
            'datasets' => [[
                'label' => 'الزيارات',
                'data' => $data,
                'backgroundColor' => '#3b82f6',
                'borderRadius' => 4,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
```

---

### تسجيل الـ Charts في صفحة الزيارات

**تعديل:** `app/Filament/Resources/PageVisitResource/Pages/ListPageVisits.php`

```diff
  protected function getHeaderWidgets(): array
  {
      return [
          \App\Filament\Widgets\VisitorStatsOverview::class,
+         \App\Filament\Widgets\VisitsLineChart::class,
+         \App\Filament\Widgets\HourlyVisitsChart::class,
+         \App\Filament\Widgets\TopPagesChart::class,
+         \App\Filament\Widgets\DeviceDistributionChart::class,
      ];
  }
```

**وإضافة `getHeaderWidgetsColumns()` في `PageVisitResource.php`:**

```php
public static function getWidgets(): array
{
    return [
        \App\Filament\Widgets\VisitorStatsOverview::class,
        \App\Filament\Widgets\VisitsLineChart::class,
        \App\Filament\Widgets\HourlyVisitsChart::class,
        \App\Filament\Widgets\TopPagesChart::class,
        \App\Filament\Widgets\DeviceDistributionChart::class,
    ];
}
```

---

## 🔷 المرحلة 1B: كشف الأجهزة والمتصفحات

### الخطوة 1: Migration — إضافة 3 أعمدة

**الأمر:** `php artisan make:migration add_device_columns_to_page_visits`

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
            $table->string('device_type', 20)->nullable()->after('user_agent');   // mobile, desktop, tablet
            $table->string('browser', 100)->nullable()->after('device_type');      // Chrome 120, Safari 17
            $table->string('os', 100)->nullable()->after('browser');               // Windows 11, Android 14

            $table->index('device_type');
        });
    }

    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropIndex(['device_type']);
            $table->dropColumn(['device_type', 'browser', 'os']);
        });
    }
};
```

### الخطوة 2: UserAgentParser Service

**الملف:** `app/Services/UserAgentParser.php`

```php
<?php

namespace App\Services;

class UserAgentParser
{
    /**
     * تحليل User Agent وإرجاع: device_type, browser, os
     */
    public static function parse(?string $userAgent): array
    {
        $ua = strtolower($userAgent ?? '');

        return [
            'device_type' => static::detectDevice($ua),
            'browser'     => static::detectBrowser($userAgent ?? ''),
            'os'          => static::detectOS($userAgent ?? ''),
        ];
    }

    protected static function detectDevice(string $ua): string
    {
        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet')
            || (str_contains($ua, 'android') && !str_contains($ua, 'mobile'))) {
            return 'tablet';
        }
        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone')
            || str_contains($ua, 'ipod') || str_contains($ua, 'android')) {
            return 'mobile';
        }
        return 'desktop';
    }

    protected static function detectBrowser(string $ua): string
    {
        $patterns = [
            '/Edg[e\/]?([\d.]+)/'    => 'Edge',
            '/OPR\/([\d.]+)/'        => 'Opera',
            '/Chrome\/([\d.]+)/'      => 'Chrome',
            '/Firefox\/([\d.]+)/'     => 'Firefox',
            '/Safari\/([\d.]+)/'      => 'Safari',
            '/MSIE ([\d.]+)/'         => 'IE',
            '/Trident.*rv:([\d.]+)/'  => 'IE',
        ];

        foreach ($patterns as $pattern => $name) {
            if (preg_match($pattern, $ua, $matches)) {
                $version = explode('.', $matches[1])[0]; // major version فقط
                return "$name $version";
            }
        }
        return 'Other';
    }

    protected static function detectOS(string $ua): string
    {
        $patterns = [
            '/Windows NT 10/'   => 'Windows 10/11',
            '/Windows NT 6.3/'  => 'Windows 8.1',
            '/Windows NT 6.1/'  => 'Windows 7',
            '/Mac OS X ([\d_]+)/' => 'macOS',
            '/Android ([\d.]+)/'  => 'Android',
            '/iPhone OS ([\d_]+)/' => 'iOS',
            '/iPad.*OS ([\d_]+)/' => 'iPadOS',
            '/Linux/'             => 'Linux',
        ];

        foreach ($patterns as $pattern => $name) {
            if (preg_match($pattern, $ua, $matches)) {
                if (isset($matches[1]) && in_array($name, ['Android', 'iOS', 'iPadOS'])) {
                    $ver = str_replace('_', '.', explode('.', $matches[1])[0]);
                    return "$name $ver";
                }
                return $name;
            }
        }
        return 'Other';
    }
}
```

### الخطوة 3: تعديل Middleware

**الملف:** `app/Http/Middleware/TrackPageVisits.php`  
**إضافة** في دالة `handle()` — داخل الـ `try` block، بعد بناء المصفوفة:

```diff
+ use App\Services\UserAgentParser;

  // داخل handle() → try block:
+ $deviceInfo = UserAgentParser::parse($request->userAgent());

  $visit = PageVisit::create([
      'session_id'  => session()->getId(),
      'ip_address'  => $request->ip(),
      // ... الحقول الموجودة ...
      'user_agent'  => $this->truncate($request->userAgent(), 512),
+     'device_type' => $deviceInfo['device_type'],
+     'browser'     => $deviceInfo['browser'],
+     'os'          => $deviceInfo['os'],
      'visited_at'  => now(),
  ]);
```

**إضافة في `PageVisit` model `$fillable`:**

```diff
  protected $fillable = [
      // ... الحقول الموجودة ...
      'user_agent',
+     'device_type',
+     'browser',
+     'os',
      'visited_at',
  ];
```

### اختبار المرحلة 1

```bash
php artisan migrate
# زُر أي صفحة في الموقع
php artisan tinker
>>> App\Models\PageVisit::latest()->first(['device_type','browser','os'])->toArray()
# يجب أن يُظهر: {"device_type":"desktop","browser":"Chrome 120","os":"Windows 10/11"}
```

---

## 🔷 المرحلة 2A: Queue Job (حفظ غير متزامن)

### الملف: `app/Jobs/RecordPageVisitJob.php`

**الأمر:** `php artisan make:job RecordPageVisitJob`

```php
<?php

namespace App\Jobs;

use App\Models\PageVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordPageVisitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected array $visitData
    ) {}

    public function handle(): void
    {
        PageVisit::create($this->visitData);
    }

    /**
     * عدد المحاولات في حالة الفشل
     */
    public int $tries = 3;
}
```

### تعديل Middleware للاستخدام مع Queue

**في `TrackPageVisits.php`** — استبدل `PageVisit::create(...)` بـ:

```diff
- $visit = PageVisit::create([...]);
+ $visitData = [
+     'session_id'  => session()->getId(),
+     'ip_address'  => $request->ip(),
+     'url'         => $this->truncate($request->fullUrl(), 2048),
+     'route_name'  => $request->route()?->getName(),
+     'page_title'  => $this->getPageTitle($request),
+     'is_bot'      => $isBot,
+     'bot_name'    => $botName,
+     'referer'     => $this->truncate($request->header('referer'), 2048),
+     'user_agent'  => $this->truncate($request->userAgent(), 512),
+     'device_type' => $deviceInfo['device_type'],
+     'browser'     => $deviceInfo['browser'],
+     'os'          => $deviceInfo['os'],
+     'visited_at'  => now(),
+ ];
+
+ // حفظ مباشر لنحتفظ بالـ visit_id للصفحة
+ $visit = PageVisit::create($visitData);
+ // أو لو ما نحتاج visit_id:
+ // \App\Jobs\RecordPageVisitJob::dispatch($visitData);
```

> [!WARNING]
> **ملاحظة مهمة:** حالياً الـ Middleware يستخدم `$visit->id` لمشاركته مع الصفحة (`view()->share('_visit_id', $visit->id)`).
> إذا استخدمنا Queue، لن نحصل على الـ `id` فوراً. لذلك **نبقي الحفظ المباشر حالياً** ونستخدم الـ Queue فقط عندما لا نحتاج `visit_id`.

---

## 🔷 المرحلة 2B: جدول الملخص اليومي

### Migration

**الأمر:** `php artisan make:migration create_visits_summary_table`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits_summary', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedInteger('total_visits')->default(0);
            $table->unsignedInteger('unique_visitors')->default(0);
            $table->unsignedInteger('unique_sessions')->default(0);
            $table->unsignedInteger('avg_duration')->default(0);
            $table->unsignedInteger('bot_visits')->default(0);
            $table->unsignedInteger('mobile_visits')->default(0);
            $table->unsignedInteger('desktop_visits')->default(0);
            $table->unsignedInteger('tablet_visits')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits_summary');
    }
};
```

### Model: `app/Models/VisitsSummary.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitsSummary extends Model
{
    protected $table = 'visits_summary';

    protected $fillable = [
        'date', 'total_visits', 'unique_visitors', 'unique_sessions',
        'avg_duration', 'bot_visits', 'mobile_visits', 'desktop_visits', 'tablet_visits',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
```

### Command: `app/Console/Commands/UpdateVisitsSummary.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\PageVisit;
use App\Models\VisitsSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateVisitsSummary extends Command
{
    protected $signature = 'visits:summarize {--date= : تاريخ محدد (Y-m-d)، الافتراضي = أمس}';
    protected $description = 'تحديث جدول ملخص الزيارات اليومي';

    public function handle(): void
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::yesterday();

        $visits = PageVisit::whereDate('visited_at', $date);

        VisitsSummary::updateOrCreate(
            ['date' => $date->format('Y-m-d')],
            [
                'total_visits'    => (clone $visits)->humans()->count(),
                'unique_visitors' => (clone $visits)->humans()->distinct('ip_address')->count('ip_address'),
                'unique_sessions' => (clone $visits)->humans()->distinct('session_id')->count('session_id'),
                'avg_duration'    => (int) ((clone $visits)->humans()
                    ->where('duration_seconds', '>', 0)->avg('duration_seconds') ?? 0),
                'bot_visits'      => (clone $visits)->bots()->count(),
                'mobile_visits'   => (clone $visits)->humans()->where('device_type', 'mobile')->count(),
                'desktop_visits'  => (clone $visits)->humans()->where('device_type', 'desktop')->count(),
                'tablet_visits'   => (clone $visits)->humans()->where('device_type', 'tablet')->count(),
            ]
        );

        $this->info("✅ تم تحديث ملخص يوم: {$date->format('Y-m-d')}");
    }
}
```

### Command: `app/Console/Commands/CleanOldVisits.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\PageVisit;
use Illuminate\Console\Command;

class CleanOldVisits extends Command
{
    protected $signature = 'visits:clean {--days=90 : حذف الزيارات الأقدم من X يوم}';
    protected $description = 'حذف سجلات الزيارات القديمة';

    public function handle(): void
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = PageVisit::where('visited_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info('لا توجد زيارات قديمة للحذف.');
            return;
        }

        if ($this->confirm("سيتم حذف {$count} زيارة أقدم من {$days} يوم. متأكد؟")) {
            PageVisit::where('visited_at', '<', $cutoff)->delete();
            $this->info("✅ تم حذف {$count} سجل.");
        }
    }
}
```

### جدولة الأوامر

**الملف:** `routes/console.php` أو `app/Console/Kernel.php`

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('visits:summarize')->dailyAt('01:00');
Schedule::command('visits:clean --days=90')->monthly();
```

### اختبار المرحلة 2

```bash
php artisan migrate
php artisan visits:summarize --date=2026-02-11
php artisan tinker
>>> App\Models\VisitsSummary::first()->toArray()
```

---

## 📋 ملخص ملفات المراحل 1-2

| # | الملف | الحالة | المرحلة |
|---|-------|--------|---------|
| 1 | `VisitsLineChart.php` | 🆕 | 1A |
| 2 | `DeviceDistributionChart.php` | 🆕 | 1A |
| 3 | `TopPagesChart.php` | 🆕 | 1A |
| 4 | `HourlyVisitsChart.php` | 🆕 | 1A |
| 5 | `ListPageVisits.php` | ✏️ تعديل | 1A |
| 6 | Migration: device columns | 🆕 | 1B |
| 7 | `UserAgentParser.php` | 🆕 | 1B |
| 8 | `TrackPageVisits.php` | ✏️ تعديل | 1B |
| 9 | `PageVisit.php` model | ✏️ تعديل | 1B |
| 10 | `RecordPageVisitJob.php` | 🆕 | 2A |
| 11 | Migration: visits_summary | 🆕 | 2B |
| 12 | `VisitsSummary.php` | 🆕 | 2B |
| 13 | `UpdateVisitsSummary.php` | 🆕 | 2B |
| 14 | `CleanOldVisits.php` | 🆕 | 2B |

> **المراحل 3-4** (GeoIP + رحلة الزائر + تحليل المصادر + Real-time + تقارير) في الملف التالي: `visitor_analytics_guide_p2.md`
