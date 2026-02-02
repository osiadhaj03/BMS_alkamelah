# 📊 خطة نظام تتبع الزوار - Filament

## 🎯 الهدف
بناء نظام بسيط لتتبع زوار الموقع وعرض الإحصائيات في لوحة تحكم Filament

---

## 📋 المتطلبات الوظيفية

### ✅ ما نريد تحقيقه:
1. **حفظ كل زيارة** تلقائياً في قاعدة البيانات
2. **معلومات الزيارة:**
   - الصفحة المزارة (URL)
   - اسم المسار (route name)
   - عنوان IP
   - نوع المتصفح (User Agent)
   - مصدر الزيارة (Referrer)
   - التاريخ والوقت
3. **عرض الإحصائيات** في Filament:
   - جدول بكل الزيارات
   - إحصائيات عامة (إجمالي، زوار فريدين، إلخ)
   - أكثر الصفحات زيارة
   - رسوم بيانية (اختياري)

---

## 🗂️ البنية التقنية

### 1. قاعدة البيانات

#### جدول: `page_visits`
```sql
CREATE TABLE page_visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(2048) NOT NULL,              -- الرابط الكامل
    route_name VARCHAR(255) NULL,            -- اسم المسار
    ip_address VARCHAR(45) NOT NULL,         -- IPv4 أو IPv6
    user_agent TEXT NULL,                    -- معلومات المتصفح
    referer VARCHAR(2048) NULL,              -- من أين جاء الزائر
    visited_at TIMESTAMP NOT NULL,           -- وقت الزيارة
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_url (url(255)),                -- للبحث السريع
    INDEX idx_route_name (route_name),       -- للإحصائيات
    INDEX idx_ip_address (ip_address),       -- لحساب الزوار الفريدين
    INDEX idx_visited_at (visited_at)        -- للفلترة حسب التاريخ
);
```

**ملاحظات:**
- `url` طويل (2048) لأن بعض URLs تحتوي query parameters
- `ip_address` يدعم IPv6 (حتى 45 حرف)
- `visited_at` منفصل عن `created_at` للدقة

---

### 2. النماذج (Models)

#### `app/Models/PageVisit.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = [
        'url',
        'route_name',
        'ip_address',
        'user_agent',
        'referer',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    // Helper: عدد الزوار الفريدين
    public static function uniqueVisitorsCount()
    {
        return static::distinct('ip_address')->count('ip_address');
    }

    // Helper: أكثر الصفحات زيارة
    public static function topPages($limit = 10)
    {
        return static::select('route_name', 'url')
            ->selectRaw('COUNT(*) as visits_count')
            ->groupBy('route_name', 'url')
            ->orderByDesc('visits_count')
            ->limit($limit)
            ->get();
    }

    // Helper: زيارات اليوم
    public static function todayVisits()
    {
        return static::whereDate('visited_at', today())->count();
    }

    // Helper: زيارات الشهر الحالي
    public static function thisMonthVisits()
    {
        return static::whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year)
            ->count();
    }
}
```

---

### 3. Middleware لتسجيل الزيارات

#### `app/Http/Middleware/TrackPageVisits.php`
```php
namespace App\Http\Middleware;

use Closure;
use App\Models\PageVisit;
use Illuminate\Http\Request;

class TrackPageVisits
{
    public function handle(Request $request, Closure $next)
    {
        // تسجيل الزيارة فقط للطلبات من نوع GET
        if ($request->isMethod('get')) {
            // استثناءات: لا نسجل هذه المسارات
            $excludedPrefixes = [
                'admin',           // لوحة التحكم
                'api',             // API routes
                'livewire',        // Livewire requests
                '_debugbar',       // Debug bar
            ];

            $path = $request->path();
            $shouldTrack = true;

            foreach ($excludedPrefixes as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    $shouldTrack = false;
                    break;
                }
            }

            // استثناء ملفات الـ Assets
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $assetExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf'];
            if (in_array($extension, $assetExtensions)) {
                $shouldTrack = false;
            }

            // حفظ الزيارة
            if ($shouldTrack) {
                try {
                    PageVisit::create([
                        'url' => $request->fullUrl(),
                        'route_name' => $request->route()?->getName(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'referer' => $request->header('referer'),
                        'visited_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    // تجاهل الأخطاء لكي لا تؤثر على تجربة المستخدم
                    \Log::error('Failed to track page visit: ' . $e->getMessage());
                }
            }
        }

        return $next($request);
    }
}
```

**تسجيل الـ Middleware:**

في `app/Http/Kernel.php` أو `bootstrap/app.php` (Laravel 11):
```php
// Laravel 10 - في Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ...
        \App\Http\Middleware\TrackPageVisits::class,
    ],
];

// Laravel 11 - في bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\TrackPageVisits::class,
    ]);
})
```

---

### 4. Filament Resource

#### `app/Filament/Resources/PageVisitResource.php`
```php
namespace App\Filament\Resources;

use App\Filament\Resources\PageVisitResource\Pages;
use App\Models\PageVisit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageVisitResource extends Resource
{
    protected static ?string $model = PageVisit::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';
    
    protected static ?string $navigationLabel = 'زيارات الصفحات';
    
    protected static ?string $navigationGroup = 'الإحصائيات';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('url')
                    ->label('الرابط')
                    ->required()
                    ->maxLength(2048),
                Forms\Components\TextInput::make('route_name')
                    ->label('اسم المسار')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ip_address')
                    ->label('عنوان IP')
                    ->required()
                    ->maxLength(45),
                Forms\Components\Textarea::make('user_agent')
                    ->label('المتصفح')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('referer')
                    ->label('المصدر')
                    ->maxLength(2048),
                Forms\Components\DateTimePicker::make('visited_at')
                    ->label('وقت الزيارة')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('الرقم')
                    ->sortable(),
                Tables\Columns\TextColumn::make('route_name')
                    ->label('الصفحة')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'home' => 'success',
                        'search.index' => 'warning',
                        'books.index' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->url),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم النسخ!')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('المتصفح')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('referer')
                    ->label('المصدر')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('visited_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->visited_at->format('Y-m-d H:i:s')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('route_name')
                    ->label('الصفحة')
                    ->options([
                        'home' => 'الصفحة الرئيسية',
                        'search.index' => 'البحث',
                        'books.index' => 'الكتب',
                        'authors.index' => 'المؤلفين',
                        'articles.index' => 'المقالات',
                        'news.index' => 'الأخبار',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('visited_at')
                    ->form([
                        Forms\Components\DatePicker::make('visited_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('visited_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['visited_from'], fn ($q) => $q->whereDate('visited_at', '>=', $data['visited_from']))
                            ->when($data['visited_until'], fn ($q) => $q->whereDate('visited_at', '<=', $data['visited_until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('visited_at', 'desc')
            ->poll('30s'); // تحديث تلقائي كل 30 ثانية
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageVisits::route('/'),
            'view' => Pages\ViewPageVisit::route('/{record}'),
        ];
    }
}
```

**إنشاء صفحات الـ Resource:**
```bash
php artisan make:filament-page ListPageVisits --resource=PageVisitResource --type=ListRecords
php artisan make:filament-page ViewPageVisit --resource=PageVisitResource --type=ViewRecord
```

---

### 5. Widgets للإحصائيات

#### A. StatsOverview Widget

**`app/Filament/Widgets/VisitorStatsOverview.php`**
```php
namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitorStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الزيارات', PageVisit::count())
                ->description('جميع الزيارات المسجلة')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 28, 35]), // بيانات وهمية

            Stat::make('زوار فريدين', PageVisit::uniqueVisitorsCount())
                ->description('عدد IPs الفريدة')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('زيارات اليوم', PageVisit::todayVisits())
                ->description('زيارات ' . now()->format('Y-m-d'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Stat::make('زيارات الشهر', PageVisit::thisMonthVisits())
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
```

**إنشاء الـ Widget:**
```bash
php artisan make:filament-widget VisitorStatsOverview --stats-overview
```

---

#### B. Top Pages Widget

**`app/Filament/Widgets/TopPagesTable.php`**
```php
namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopPagesTable extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('أكثر الصفحات زيارة')
            ->query(
                PageVisit::query()
                    ->select('route_name', 'url')
                    ->selectRaw('COUNT(*) as visits_count')
                    ->groupBy('route_name', 'url')
                    ->orderByDesc('visits_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('route_name')
                    ->label('اسم الصفحة')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->limit(60),
                Tables\Columns\TextColumn::make('visits_count')
                    ->label('عدد الزيارات')
                    ->sortable()
                    ->badge()
                    ->color('info'),
            ]);
    }
}
```

---

#### C. Visits Chart Widget (اختياري)

**`app/Filament/Widgets/VisitsChart.php`**
```php
namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VisitsChart extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected static ?string $heading = 'الزيارات آخر 30 يوم';

    protected function getData(): array
    {
        $data = PageVisit::query()
            ->select(DB::raw('DATE(visited_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('visited_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // ملء الأيام المفقودة بـ 0
        $labels = [];
        $values = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('M d');
            $values[] = $data[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'الزيارات',
                    'data' => $values,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
```

---

## 🚀 خطوات التنفيذ

### المرحلة 1: الإعداد الأساسي ✅
```bash
# 1. إنشاء Migration
php artisan make:migration create_page_visits_table

# 2. تعديل الـ Migration (نسخ الكود أعلاه)

# 3. تشغيل الـ Migration
php artisan migrate

# 4. إنشاء Model
php artisan make:model PageVisit
```

### المرحلة 2: Middleware ✅
```bash
# 1. إنشاء Middleware
php artisan make:middleware TrackPageVisits

# 2. تعديل الـ Middleware (نسخ الكود أعلاه)

# 3. تسجيل الـ Middleware في Kernel.php
```

### المرحلة 3: Filament Resource ✅
```bash
# 1. إنشاء Resource
php artisan make:filament-resource PageVisit --generate

# 2. تعديل الـ Resource (نسخ الكود أعلاه)
```

### المرحلة 4: Widgets ✅
```bash
# 1. Stats Widget
php artisan make:filament-widget VisitorStatsOverview --stats-overview

# 2. Table Widget
php artisan make:filament-widget TopPagesTable --table

# 3. Chart Widget (اختياري)
php artisan make:filament-widget VisitsChart --chart
```

### المرحلة 5: الاختبار ✅
1. زيارة صفحات مختلفة في الموقع
2. فحص قاعدة البيانات: `SELECT * FROM page_visits ORDER BY id DESC LIMIT 10`
3. الدخول إلى Filament Admin Panel
4. التحقق من عرض البيانات في الجداول والإحصائيات

---

## 📊 النتيجة المتوقعة

### في Filament Dashboard:
```
┌──────────────────────────────────────────────────────┐
│  إحصائيات الزوار                                     │
├─────────────┬─────────────┬────────────┬────────────┤
│ إجمالي      │ زوار فريدين │ اليوم      │ الشهر      │
│ 15,420      │ 3,245       │ 156        │ 4,890      │
└─────────────┴─────────────┴────────────┴────────────┘

┌──────────────────────────────────────────────────────┐
│  أكثر الصفحات زيارة                                  │
│  1. home             - 5,230 زيارة                   │
│  2. search.index     - 3,450 زيارة                   │
│  3. books.index      - 2,100 زيارة                   │
└──────────────────────────────────────────────────────┘
```

### جدول الزيارات:
- عرض جميع الزيارات
- فلترة حسب: الصفحة، التاريخ
- بحث في URL و IP
- تصدير إلى Excel

---

## ⚠️ ملاحظات مهمة

### الأداء:
1. **الجدول سيكبر بسرعة:**
   - احذف الزيارات القديمة شهرياً
   - أو انقلها إلى جدول أرشيف
   
2. **تأثير الـ Middleware:**
   - يضيف ~5-10ms لكل طلب
   - استخدم Queue Jobs إذا صار بطيء

3. **Ad Blockers:**
   - بعض المستخدمين لن يتم تسجيلهم
   - هذا طبيعي في جميع أنظمة التتبع

### الأمان:
- ✅ لا نحفظ معلومات شخصية
- ✅ IP Addresses فقط للإحصائيات
- ✅ احترام الخصوصية

---

## 🔄 تحسينات مستقبلية (اختيارية)

### 1. تحديد الدولة من IP
```bash
composer require geoip2/geoip2
```

### 2. Real-time Dashboard
- استخدام Livewire polling
- تحديث تلقائي كل دقيقة

### 3. تقارير متقدمة
- PDF Export
- Email Reports
- Scheduled Reports

### 4. تحليلات متقدمة
- معدل الارتداد (Bounce Rate)
- متوسط مدة الجلسة
- مسار المستخدم (User Journey)

---

## ✅ Checklist التنفيذ

- [ ] إنشاء Migration
- [ ] إنشاء Model
- [ ] إنشاء Middleware
- [ ] تسجيل Middleware
- [ ] اختبار التسجيل
- [ ] إنشاء Filament Resource
- [ ] إنشاء Stats Widget
- [ ] إنشاء Top Pages Widget
- [ ] إنشاء Chart Widget (اختياري)
- [ ] اختبار نهائي
- [ ] Deploy

---

## 📞 الدعم

في حال واجهت مشاكل:
1. تحقق من logs: `storage/logs/laravel.log`
2. تحقق من الـ Middleware مسجل صح
3. تحقق من الـ Migration نفذت
4. تحقق من Filament مثبت ومشتغل

---

**تاريخ الإنشاء:** 2026-01-28
**الحالة:** جاهز للتنفيذ ✅
