# 📊 خطة نظام تتبع الزوار المحسّنة - Filament

## 🎯 الهدف

بناء نظام متقدم وعالي الأداء لتتبع زوار الموقع وعرض الإحصائيات في لوحة تحكم Filament

---

## 📋 المتطلبات الوظيفية

### ✅ ما نريد تحقيقه

1. **حفظ كل زيارة** تلقائياً في قاعدة البيانات (بدون تأثير على الأداء)
2. **معلومات الزيارة:**
   - الصفحة المزارة (URL)
   - عنوان الصفحة (Page Title)
   - اسم المسار (route name)
   - Session ID (لتتبع الجلسات)
   - عنوان IP
   - نوع المتصفح (User Agent)
   - مصدر الزيارة (Referrer)
   - اكتشاف الـ Bots
   - التاريخ والوقت
3. **عرض الإحصائيات** في Filament:
   - جدول بكل الزيارات
   - إحصائيات عامة (إجمالي، زوار فريدين، جلسات، إلخ)
   - أكثر الصفحات زيارة
   - رسوم بيانية
   - إحصائيات Bots منفصلة
4. **تحسينات الأداء:**
   - Queue Jobs للحفظ غير المتزامن
   - Cache للإحصائيات
   - تنظيف تلقائي للبيانات القديمة

---

## 🗂️ البنية التقنية

### 1. قاعدة البيانات

#### جدول: `page_visits`

```sql
CREATE TABLE page_visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(2048) NOT NULL,              -- الرابط الكامل
    page_title VARCHAR(255) NULL,            -- عنوان الصفحة
    route_name VARCHAR(255) NULL,            -- اسم المسار
    session_id VARCHAR(255) NULL,            -- معرف الجلسة
    ip_address VARCHAR(45) NOT NULL,         -- IPv4 أو IPv6
    user_agent TEXT NULL,                    -- معلومات المتصفح
    referer VARCHAR(2048) NULL,              -- من أين جاء الزائر
    is_bot BOOLEAN DEFAULT FALSE,            -- هل هو Bot
    bot_name VARCHAR(100) NULL,              -- اسم الـ Bot
    visited_at TIMESTAMP NOT NULL,           -- وقت الزيارة
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_url (url(255)),                -- للبحث السريع
    INDEX idx_route_name (route_name),       -- للإحصائيات
    INDEX idx_session_id (session_id),       -- لتتبع الجلسات
    INDEX idx_ip_address (ip_address),       -- لحساب الزوار الفريدين
    INDEX idx_visited_at (visited_at),       -- للفلترة حسب التاريخ
    INDEX idx_is_bot (is_bot)                -- لفصل Bots عن الزوار
);
```

**ملاحظات:**

- `url` طويل (2048) لأن بعض URLs تحتوي query parameters
- `ip_address` يدعم IPv6 (حتى 45 حرف)
- `session_id` لتتبع رحلة المستخدم عبر الصفحات
- `is_bot` لفصل الـ Bots عن الزوار الحقيقيين
- `visited_at` منفصل عن `created_at` للدقة

#### جدول: `visits_summary` (للأداء)

```sql
CREATE TABLE visits_summary (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL UNIQUE,               -- التاريخ
    total_visits INT UNSIGNED DEFAULT 0,     -- إجمالي الزيارات
    unique_visitors INT UNSIGNED DEFAULT 0,  -- زوار فريدين
    unique_sessions INT UNSIGNED DEFAULT 0,  -- جلسات فريدة
    bot_visits INT UNSIGNED DEFAULT 0,       -- زيارات Bots
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_date (date)
);
```

**الفائدة:** بدلاً من حساب الإحصائيات من جدول ضخم كل مرة، نحسبها مرة واحدة يومياً ونخزنها.

---

### 2. النماذج (Models)

#### `app/Models/PageVisit.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageVisit extends Model
{
    protected $fillable = [
        'url',
        'page_title',
        'route_name',
        'session_id',
        'ip_address',
        'user_agent',
        'referer',
        'is_bot',
        'bot_name',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'is_bot' => 'boolean',
    ];

    // Scopes
    public function scopeHumans($query)
    {
        return $query->where('is_bot', false);
    }

    public function scopeBots($query)
    {
        return $query->where('is_bot', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year);
    }

    // Helper: عدد الزوار الفريدين (مع Cache)
    public static function uniqueVisitorsCount(int $cacheDuration = 3600)
    {
        return Cache::remember('unique_visitors_count', $cacheDuration, function () {
            return static::humans()->distinct('ip_address')->count('ip_address');
        });
    }

    // Helper: عدد الجلسات الفريدة (مع Cache)
    public static function uniqueSessionsCount(int $cacheDuration = 3600)
    {
        return Cache::remember('unique_sessions_count', $cacheDuration, function () {
            return static::humans()->distinct('session_id')->count('session_id');
        });
    }

    // Helper: أكثر الصفحات زيارة
    public static function topPages($limit = 10, $botsIncluded = false)
    {
        $query = static::select('route_name', 'page_title', 'url')
            ->selectRaw('COUNT(*) as visits_count');

        if (!$botsIncluded) {
            $query->where('is_bot', false);
        }

        return $query->groupBy('route_name', 'page_title', 'url')
            ->orderByDesc('visits_count')
            ->limit($limit)
            ->get();
    }

    // Helper: زيارات اليوم
    public static function todayVisits()
    {
        return static::humans()->whereDate('visited_at', today())->count();
    }

    // Helper: زيارات الشهر الحالي
    public static function thisMonthVisits()
    {
        return static::humans()->thisMonth()->count();
    }

    // Helper: معدل الارتداد (Bounce Rate)
    public static function bounceRate()
    {
        $singlePageSessions = static::humans()
            ->select('session_id')
            ->groupBy('session_id')
            ->havingRaw('COUNT(*) = 1')
            ->count();

        $totalSessions = static::uniqueSessionsCount();

        return $totalSessions > 0 
            ? round(($singlePageSessions / $totalSessions) * 100, 2) 
            : 0;
    }

    // Helper: متوسط الصفحات في الجلسة
    public static function avgPagesPerSession()
    {
        $totalPages = static::humans()->count();
        $totalSessions = static::uniqueSessionsCount();

        return $totalSessions > 0 
            ? round($totalPages / $totalSessions, 2) 
            : 0;
    }
}
```

#### `app/Models/VisitsSummary.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitsSummary extends Model
{
    protected $table = 'visits_summary';

    protected $fillable = [
        'date',
        'total_visits',
        'unique_visitors',
        'unique_sessions',
        'bot_visits',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
```

---

### 3. Queue Job للحفظ غير المتزامن

#### `app/Jobs/RecordPageVisitJob.php`

```php
namespace App\Jobs;

use App\Models\PageVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecordPageVisitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    protected array $visitData;

    public function __construct(array $visitData)
    {
        $this->visitData = $visitData;
    }

    public function handle(): void
    {
        try {
            PageVisit::create($this->visitData);
        } catch (\Exception $e) {
            Log::error('Failed to record page visit', [
                'data' => $this->visitData,
                'error' => $e->getMessage(),
            ]);
            
            // Re-throw لإعادة المحاولة
            throw $e;
        }
    }
}
```

---

### 4. Middleware لتسجيل الزيارات

#### `app/Http/Middleware/TrackPageVisits.php`

```php
namespace App\Http\Middleware;

use Closure;
use App\Jobs\RecordPageVisitJob;
use Illuminate\Http\Request;

class TrackPageVisits
{
    /**
     * قائمة Bot Patterns المعروفة
     */
    protected array $botPatterns = [
        'googlebot', 'bingbot', 'slurp', 'duckduckbot',
        'baiduspider', 'yandexbot', 'crawler', 'spider',
        'bot', 'facebookexternalhit', 'twitterbot',
        'whatsapp', 'telegram', 'slack', 'discord',
        'linkedin', 'pinterest', 'archive.org',
    ];

    /**
     * المسارات المستثناة من التتبع
     */
    protected array $excludedPrefixes = [
        'admin',           // لوحة التحكم
        'api',             // API routes
        'livewire',        // Livewire requests
        '_debugbar',       // Debug bar
        'telescope',       // Telescope
        'horizon',         // Horizon
        'pulse',           // Pulse
    ];

    /**
     * امتدادات الملفات المستثناة
     */
    protected array $assetExtensions = [
        'css', 'js', 'jpg', 'jpeg', 'png', 'gif', 
        'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot',
        'json', 'xml', 'pdf', 'zip', 'txt',
    ];

    public function handle(Request $request, Closure $next)
    {
        // تسجيل الزيارة فقط للطلبات من نوع GET
        if ($request->isMethod('get') && $this->shouldTrack($request)) {
            // جمع بيانات الزيارة
            $visitData = $this->prepareVisitData($request);
            
            // إرسال إلى Queue للحفظ غير المتزامن
            RecordPageVisitJob::dispatch($visitData);
        }

        return $next($request);
    }

    /**
     * التحقق من ضرورة تتبع هذا الطلب
     */
    protected function shouldTrack(Request $request): bool
    {
        $path = $request->path();

        // استثناء المسارات المحددة
        foreach ($this->excludedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return false;
            }
        }

        // استثناء ملفات الـ Assets
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array($extension, $this->assetExtensions)) {
            return false;
        }

        return true;
    }

    /**
     * تجهيز بيانات الزيارة
     */
    protected function prepareVisitData(Request $request): array
    {
        [$isBot, $botName] = $this->detectBot($request);

        return [
            'url' => $request->fullUrl(),
            'page_title' => $this->getPageTitle($request),
            'route_name' => $request->route()?->getName(),
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'is_bot' => $isBot,
            'bot_name' => $botName,
            'visited_at' => now(),
        ];
    }

    /**
     * اكتشاف الـ Bots
     */
    protected function detectBot(Request $request): array
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return [true, $this->extractBotName($userAgent, $pattern)];
            }
        }

        return [false, null];
    }

    /**
     * استخراج اسم الـ Bot
     */
    protected function extractBotName(string $userAgent, string $pattern): string
    {
        // محاولة استخراج اسم أدق
        if (preg_match('/(' . $pattern . '[^\s;)]*)/i', $userAgent, $matches)) {
            return ucfirst($matches[1]);
        }

        return ucfirst($pattern);
    }

    /**
     * الحصول على عنوان الصفحة
     */
    protected function getPageTitle(Request $request): ?string
    {
        return match($request->route()?->getName()) {
            'home' => 'الصفحة الرئيسية',
            'search.index' => 'البحث',
            'search.content' => 'البحث في المحتوى',
            'books.index' => 'تصفح الكتب',
            'books.show' => 'عرض كتاب',
            'authors.index' => 'المؤلفين',
            'authors.show' => 'عرض مؤلف',
            'articles.index' => 'المقالات',
            'articles.show' => 'عرض مقالة',
            'news.index' => 'الأخبار',
            'news.show' => 'عرض خبر',
            'about' => 'عن الموقع',
            'contact' => 'اتصل بنا',
            default => null,
        };
    }
}
```

**تسجيل الـ Middleware:**

في `bootstrap/app.php` (Laravel 11):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\TrackPageVisits::class,
    ]);
})
```

أو في `app/Http/Kernel.php` (Laravel 10):

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \App\Http\Middleware\TrackPageVisits::class,
    ],
];
```

---

### 5. Console Commands

#### A. تنظيف الزيارات القديمة

**`app/Console/Commands/CleanOldVisits.php`**

```php
namespace App\Console\Commands;

use App\Models\PageVisit;
use Illuminate\Console\Command;

class CleanOldVisits extends Command
{
    protected $signature = 'visits:clean 
                            {--days=90 : عدد الأيام للاحتفاظ بالبيانات}
                            {--dry-run : معاينة بدون حذف}';

    protected $description = 'حذف زيارات الصفحات الأقدم من عدد الأيام المحدد';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $isDryRun = $this->option('dry-run');

        $this->info("🗑️ البحث عن زيارات أقدم من {$days} يوم...");

        $query = PageVisit::where('visited_at', '<', now()->subDays($days));
        $count = $query->count();

        if ($count === 0) {
            $this->info('✅ لا توجد زيارات قديمة للحذف');
            return Command::SUCCESS;
        }

        if ($isDryRun) {
            $this->warn("⚠️ وضع المعاينة: سيتم حذف {$count} زيارة");
            return Command::SUCCESS;
        }

        if ($this->confirm("هل تريد حذف {$count} زيارة؟", true)) {
            $deleted = $query->delete();
            $this->info("✅ تم حذف {$deleted} زيارة");
            
            // تحسين الجدول
            $this->info('🔧 جاري تحسين الجدول...');
            \DB::statement('OPTIMIZE TABLE page_visits');
            
            $this->info('✅ تم التحسين بنجاح');
        } else {
            $this->warn('❌ تم الإلغاء');
        }

        return Command::SUCCESS;
    }
}
```

#### B. تحديث ملخص الزيارات اليومي

**`app/Console/Commands/UpdateVisitsSummary.php`**

```php
namespace App\Console\Commands;

use App\Models\PageVisit;
use App\Models\VisitsSummary;
use Illuminate\Console\Command;

class UpdateVisitsSummary extends Command
{
    protected $signature = 'visits:summarize 
                            {--date= : التاريخ (Y-m-d) الافتراضي: أمس}';

    protected $description = 'حساب وحفظ ملخص الزيارات اليومي';

    public function handle(): int
    {
        $date = $this->option('date') 
            ? \Carbon\Carbon::parse($this->option('date')) 
            : now()->subDay();

        $this->info("📊 حساب ملخص الزيارات ليوم: {$date->format('Y-m-d')}");

        $totalVisits = PageVisit::whereDate('visited_at', $date)->count();
        
        $uniqueVisitors = PageVisit::whereDate('visited_at', $date)
            ->where('is_bot', false)
            ->distinct('ip_address')
            ->count('ip_address');
        
        $uniqueSessions = PageVisit::whereDate('visited_at', $date)
            ->where('is_bot', false)
            ->distinct('session_id')
            ->count('session_id');
        
        $botVisits = PageVisit::whereDate('visited_at', $date)
            ->where('is_bot', true)
            ->count();

        VisitsSummary::updateOrCreate(
            ['date' => $date->format('Y-m-d')],
            [
                'total_visits' => $totalVisits,
                'unique_visitors' => $uniqueVisitors,
                'unique_sessions' => $uniqueSessions,
                'bot_visits' => $botVisits,
            ]
        );

        $this->info("✅ تم حفظ الملخص:");
        $this->table(
            ['المؤشر', 'القيمة'],
            [
                ['إجمالي الزيارات', $totalVisits],
                ['زوار فريدين', $uniqueVisitors],
                ['جلسات فريدة', $uniqueSessions],
                ['زيارات Bots', $botVisits],
            ]
        );

        return Command::SUCCESS;
    }
}
```

#### C. إحصائيات سريعة

**`app/Console/Commands/VisitsStats.php`**

```php
namespace App\Console\Commands;

use App\Models\PageVisit;
use Illuminate\Console\Command;

class VisitsStats extends Command
{
    protected $signature = 'visits:stats';
    protected $description = 'عرض إحصائيات سريعة عن الزيارات';

    public function handle(): int
    {
        $this->info('📊 إحصائيات الزيارات');
        $this->newLine();

        $stats = [
            ['المؤشر', 'القيمة'],
            ['إجمالي الزيارات', number_format(PageVisit::count())],
            ['زيارات البشر', number_format(PageVisit::humans()->count())],
            ['زيارات Bots', number_format(PageVisit::bots()->count())],
            ['زوار فريدين', number_format(PageVisit::uniqueVisitorsCount())],
            ['جلسات فريدة', number_format(PageVisit::uniqueSessionsCount())],
            ['زيارات اليوم', number_format(PageVisit::todayVisits())],
            ['زيارات الشهر', number_format(PageVisit::thisMonthVisits())],
            ['معدل الارتداد', PageVisit::bounceRate() . '%'],
            ['متوسط الصفحات/جلسة', PageVisit::avgPagesPerSession()],
        ];

        $this->table($stats[0], array_slice($stats, 1));

        $this->newLine();
        $this->info('🔝 أكثر 5 صفحات زيارة:');
        
        $topPages = PageVisit::topPages(5);
        $topPagesData = $topPages->map(fn($page) => [
            $page->page_title ?? $page->route_name ?? '-',
            number_format($page->visits_count),
        ]);

        $this->table(['الصفحة', 'الزيارات'], $topPagesData);

        return Command::SUCCESS;
    }
}
```

**تسجيل الـ Commands في Scheduler:**

في `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // تحديث الملخص اليومي في الساعة 1 صباحاً
    $schedule->command('visits:summarize')->dailyAt('01:00');
    
    // حذف الزيارات الأقدم من 90 يوم - شهرياً
    $schedule->command('visits:clean --days=90')->monthly();
    
    // مسح الـ Cache كل ساعة
    $schedule->call(function () {
        Cache::forget('unique_visitors_count');
        Cache::forget('unique_sessions_count');
    })->hourly();
}
```

---

### 6. Filament Resource

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
                    ->maxLength(2048)
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('page_title')
                    ->label('عنوان الصفحة')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('route_name')
                    ->label('اسم المسار')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('session_id')
                    ->label('معرف الجلسة')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('ip_address')
                    ->label('عنوان IP')
                    ->required()
                    ->maxLength(45),
                    
                Forms\Components\Toggle::make('is_bot')
                    ->label('Bot؟')
                    ->default(false),
                    
                Forms\Components\TextInput::make('bot_name')
                    ->label('اسم الـ Bot')
                    ->maxLength(100),
                    
                Forms\Components\Textarea::make('user_agent')
                    ->label('المتصفح')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('referer')
                    ->label('المصدر')
                    ->maxLength(2048)
                    ->columnSpanFull(),
                    
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\IconColumn::make('is_bot')
                    ->label('🤖')
                    ->boolean()
                    ->tooltip(fn ($record) => $record->is_bot 
                        ? "Bot: {$record->bot_name}" 
                        : 'زائر بشري'
                    )
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('page_title')
                    ->label('الصفحة')
                    ->searchable()
                    ->sortable()
                    ->default(fn ($record) => $record->route_name ?? '-')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                    
                Tables\Columns\TextColumn::make('route_name')
                    ->label('المسار')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->url)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('session_id')
                    ->label('الجلسة')
                    ->searchable()
                    ->limit(10)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
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
                Tables\Filters\TernaryFilter::make('is_bot')
                    ->label('نوع الزائر')
                    ->placeholder('الكل')
                    ->trueLabel('Bots فقط')
                    ->falseLabel('بشر فقط'),
                    
                Tables\Filters\SelectFilter::make('route_name')
                    ->label('الصفحة')
                    ->options([
                        'home' => 'الصفحة الرئيسية',
                        'search.index' => 'البحث',
                        'search.content' => 'البحث في المحتوى',
                        'books.index' => 'الكتب',
                        'books.show' => 'عرض كتاب',
                        'authors.index' => 'المؤلفين',
                        'authors.show' => 'عرض مؤلف',
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageVisits::route('/'),
            'view' => Pages\ViewPageVisit::route('/{record}'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return (string) PageVisit::todayVisits();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
```

---

### 7. Widgets للإحصائيات

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
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الزيارات', number_format(PageVisit::count()))
                ->description('جميع الزيارات المسجلة')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($this->getWeeklyChart()),

            Stat::make('زوار فريدين', number_format(PageVisit::uniqueVisitorsCount()))
                ->description('عدد IPs الفريدة (بشر فقط)')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('جلسات فريدة', number_format(PageVisit::uniqueSessionsCount()))
                ->description('عدد الجلسات الفريدة')
                ->descriptionIcon('heroicon-m-cursor-arrow-ripple')
                ->color('warning'),

            Stat::make('زيارات اليوم', number_format(PageVisit::todayVisits()))
                ->description('زيارات ' . now()->format('Y-m-d'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('زيارات الشهر', number_format(PageVisit::thisMonthVisits()))
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('معدل الارتداد', PageVisit::bounceRate() . '%')
                ->description('نسبة الجلسات ذات الصفحة الواحدة')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color(function ($state) {
                    $rate = (float) $state;
                    if ($rate < 40) return 'success';
                    if ($rate < 70) return 'warning';
                    return 'danger';
                }),

            Stat::make('متوسط الصفحات/جلسة', PageVisit::avgPagesPerSession())
                ->description('عدد الصفحات المتوسط في كل جلسة')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('زيارات Bots', number_format(PageVisit::bots()->count()))
                ->description('الروبوتات ومحركات البحث')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('gray'),
        ];
    }

    /**
     * الحصول على بيانات آخر 7 أيام للرسم البياني
     */
    protected function getWeeklyChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = PageVisit::humans()
                ->whereDate('visited_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
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
            ->heading('أكثر الصفحات زيارة (آخر 30 يوم)')
            ->query(
                PageVisit::query()
                    ->where('is_bot', false)
                    ->where('visited_at', '>=', now()->subDays(30))
                    ->select('route_name', 'page_title', 'url')
                    ->selectRaw('COUNT(*) as visits_count')
                    ->selectRaw('COUNT(DISTINCT session_id) as sessions_count')
                    ->groupBy('route_name', 'page_title', 'url')
                    ->orderByDesc('visits_count')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('page_title')
                    ->label('اسم الصفحة')
                    ->default(fn ($record) => $record->route_name ?? '-')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->url),
                    
                Tables\Columns\TextColumn::make('visits_count')
                    ->label('عدد الزيارات')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => number_format($state)),
                    
                Tables\Columns\TextColumn::make('sessions_count')
                    ->label('الجلسات')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => number_format($state)),
            ]);
    }
}
```

---

#### C. Visits Chart Widget

**`app/Filament/Widgets/VisitsChart.php`**

```php
namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VisitsChart extends ChartWidget
{
    protected static ?int $sort = 3;
    
    protected static ?string $heading = 'الزيارات آخر 30 يوم';
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // جلب بيانات آخر 30 يوم
        $humansData = PageVisit::query()
            ->select(DB::raw('DATE(visited_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('visited_at', '>=', now()->subDays(30))
            ->where('is_bot', false)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $botsData = PageVisit::query()
            ->select(DB::raw('DATE(visited_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('visited_at', '>=', now()->subDays(30))
            ->where('is_bot', true)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // ملء الأيام المفقودة بـ 0
        $labels = [];
        $humansValues = [];
        $botsValues = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('M d');
            $humansValues[] = $humansData[$date] ?? 0;
            $botsValues[] = $botsData[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'زوار بشريين',
                    'data' => $humansValues,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Bots',
                    'data' => $botsValues,
                    'borderColor' => 'rgb(156, 163, 175)',
                    'backgroundColor' => 'rgba(156, 163, 175, 0.1)',
                    'fill' => true,
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

#### D. Top Bots Widget

**`app/Filament/Widgets/TopBotsTable.php`**

```php
namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopBotsTable extends BaseWidget
{
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('أكثر الـ Bots نشاطاً (آخر 30 يوم)')
            ->query(
                PageVisit::query()
                    ->where('is_bot', true)
                    ->where('visited_at', '>=', now()->subDays(30))
                    ->select('bot_name')
                    ->selectRaw('COUNT(*) as visits_count')
                    ->selectRaw('COUNT(DISTINCT ip_address) as unique_ips')
                    ->groupBy('bot_name')
                    ->orderByDesc('visits_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('bot_name')
                    ->label('اسم الـ Bot')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-cpu-chip'),
                    
                Tables\Columns\TextColumn::make('visits_count')
                    ->label('عدد الزيارات')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => number_format($state)),
                    
                Tables\Columns\TextColumn::make('unique_ips')
                    ->label('عناوين IP فريدة')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => number_format($state)),
            ]);
    }
}
```

---

## 🚀 خطوات التنفيذ

### المرحلة 1: الإعداد الأساسي ✅

```bash
# 1. إنشاء Migrations
php artisan make:migration create_page_visits_table
php artisan make:migration create_visits_summary_table

# 2. تعديل الـ Migrations (نسخ الكود أعلاه)

# 3. تشغيل الـ Migrations
php artisan migrate

# 4. إنشاء Models
php artisan make:model PageVisit
php artisan make:model VisitsSummary
```

---

### المرحلة 2: Queue Job ✅

```bash
# 1. إنشاء Job
php artisan make:job RecordPageVisitJob

# 2. تعديل الـ Job (نسخ الكود أعلاه)

# 3. التأكد من تشغيل Queue Worker
php artisan queue:work

# أو في Production: استخدم Supervisor أو Laravel Horizon
```

---

### المرحلة 3: Middleware ✅

```bash
# 1. إنشاء Middleware
php artisan make:middleware TrackPageVisits

# 2. تعديل الـ Middleware (نسخ الكود أعلاه)

# 3. تسجيل الـ Middleware في bootstrap/app.php أو Kernel.php
```

---

### المرحلة 4: Console Commands ✅

```bash
# إنشاء Commands
php artisan make:command CleanOldVisits
php artisan make:command UpdateVisitsSummary
php artisan make:command VisitsStats

# تعديل Commands (نسخ الكود أعلاه)

# تسجيل في Kernel.php للـ Scheduler
```

---

### المرحلة 5: Filament Resource ✅

```bash
# 1. إنشاء Resource
php artisan make:filament-resource PageVisit --generate

# 2. تعديل الـ Resource (نسخ الكود أعلاه)
```

---

### المرحلة 6: Widgets ✅

```bash
# 1. Stats Widget
php artisan make:filament-widget VisitorStatsOverview --stats-overview

# 2. Top Pages Widget
php artisan make:filament-widget TopPagesTable --table

# 3. Chart Widget
php artisan make:filament-widget VisitsChart --chart

# 4. Top Bots Widget
php artisan make:filament-widget TopBotsTable --table
```

---

### المرحلة 7: الاختبار ✅

```bash
# 1. اختبار التسجيل
- زيارة صفحات مختلفة في الموقع
- فحص Queue: php artisan queue:work --once
- فحص قاعدة البيانات: SELECT * FROM page_visits ORDER BY id DESC LIMIT 10

# 2. اختبار Commands
php artisan visits:stats
php artisan visits:clean --days=90 --dry-run
php artisan visits:summarize

# 3. الدخول إلى Filament Admin Panel
- التحقق من عرض البيانات في الجداول
- التحقق من الإحصائيات
- التحقق من الرسوم البيانية
```

---

## 📊 النتيجة المتوقعة

### في Filament Dashboard

```
┌───────────────────────────────────────────────────────────────────┐
│  إحصائيات الزوار                                                  │
├──────────────┬──────────────┬──────────────┬─────────────────────┤
│ إجمالي       │ زوار فريدين  │ جلسات فريدة  │ اليوم               │
│ 45,230       │ 12,345       │ 15,890       │ 456                 │
├──────────────┼──────────────┼──────────────┼─────────────────────┤
│ الشهر        │ معدل الارتداد│ صفحات/جلسة   │ Bots                │
│ 18,920       │ 35.5%        │ 3.2          │ 8,450               │
└──────────────┴──────────────┴──────────────┴─────────────────────┘

┌───────────────────────────────────────────────────────────────────┐
│  أكثر الصفحات زيارة (آخر 30 يوم)                                 │
│  1. الصفحة الرئيسية          - 12,530 زيارة - 8,240 جلسة         │
│  2. البحث                     - 8,450 زيارة  - 6,120 جلسة         │
│  3. تصفح الكتب                - 5,100 زيارة  - 3,890 جلسة         │
└───────────────────────────────────────────────────────────────────┘

┌───────────────────────────────────────────────────────────────────┐
│  أكثر الـ Bots نشاطاً                                             │
│  1. Googlebot                 - 3,210 زيارة                       │
│  2. Bingbot                   - 1,450 زيارة                       │
│  3. YandexBot                 - 890 زيارة                         │
└───────────────────────────────────────────────────────────────────┘
```

### جدول الزيارات

- عرض جميع الزيارات مع تمييز Bots
- فلترة حسب: الصفحة، التاريخ، نوع الزائر
- بحث في URL، IP، Session
- تحديث تلقائي كل 30 ثانية
- تصدير إلى Excel

---

## ⚠️ ملاحظات مهمة

### الأداء

1. **استخدام Queue Jobs:**
   - ✅ لا يؤخر الاستجابة للزائر
   - ✅ يتحمل الضغط العالي
   - ⚠️ يحتاج Queue Worker يعمل دائماً

2. **الجدول سيكبر بسرعة:**
   - استخدم Command التنظيف شهرياً
   - أو انقل البيانات لجدول أرشيف
   - استخدم `visits_summary` للإحصائيات التاريخية

3. **Cache الإحصائيات:**
   - ✅ يسرّع عرض Dashboard
   - ⚠️ قد تكون البيانات متأخرة ساعة واحدة (حسب TTL)

### الأمان والخصوصية

- ✅ لا نحفظ معلومات شخصية
- ✅ IP Addresses فقط للإحصائيات
- ✅ Session IDs لا تربط بمستخدمين محددين
- ✅ احترام GDPR (إذا طُبّق)

### Queue Configuration

في `.env`:

```env
QUEUE_CONNECTION=database  # أو redis للأداء الأفضل
```

إذا استخدمت `database`:

```bash
php artisan queue:table
php artisan migrate
```

في Production:

```bash
# استخدم Supervisor أو Laravel Horizon
php artisan queue:work --tries=3 --timeout=30
```

---

## 🔄 تحسينات مستقبلية (اختيارية)

### 1. تحديد الدولة من IP

```bash
composer require geoip2/geoip2
```

إضافة حقل `country_code` في الجدول:

```sql
ALTER TABLE page_visits ADD COLUMN country_code VARCHAR(2) NULL AFTER ip_address;
```

### 2. User Journey Tracking

تتبع مسار المستخدم عبر الصفحات في نفس الجلسة:

```sql
CREATE TABLE user_journeys (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    pages TEXT NOT NULL,  -- JSON array of page visits
    duration INT NULL,    -- Session duration in seconds
    created_at TIMESTAMP NULL
);
```

### 3. Real-time Dashboard

- استخدام Livewire polling
- WebSocket للتحديثات الفورية
- عرض الزوار الحاليين (آخر 5 دقائق)

### 4. تقارير متقدمة

- PDF Export
- Email Reports (Weekly/Monthly)
- Scheduled Reports
- Custom Date Ranges

### 5. A/B Testing Integration

- تتبع التجارب
- تحليل النتائج
- معدلات التحويل

### 6. Heatmaps (خرائط حرارية)

- تتبع النقرات
- تتبع حركة الماوس
- تحليل سلوك المستخدم

---

## ✅ Checklist التنفيذ

- [ ] إنشاء Migration: `page_visits`
- [ ] إنشاء Migration: `visits_summary`
- [ ] تشغيل Migrations
- [ ] إنشاء Model: `PageVisit`
- [ ] إنشاء Model: `VisitsSummary`
- [ ] إنشاء Job: `RecordPageVisitJob`
- [ ] إنشاء Middleware: `TrackPageVisits`
- [ ] تسجيل Middleware
- [ ] إنشاء Command: `CleanOldVisits`
- [ ] إنشاء Command: `UpdateVisitsSummary`
- [ ] إنشاء Command: `VisitsStats`
- [ ] تسجيل Commands في Scheduler
- [ ] اختبار التسجيل
- [ ] إنشاء Filament Resource
- [ ] إنشاء Widget: `VisitorStatsOverview`
- [ ] إنشاء Widget: `TopPagesTable`
- [ ] إنشاء Widget: `VisitsChart`
- [ ] إنشاء Widget: `TopBotsTable`
- [ ] تشغيل Queue Worker
- [ ] اختبار نهائي
- [ ] Deploy to Production
- [ ] إعداد Supervisor/Horizon للـ Queue
- [ ] إعداد Cron Jobs للـ Scheduler

---

## 🛠️ Troubleshooting

### المشكلة: الزيارات لا تُسجل

**الحل:**

1. تحقق من أن الـ Queue Worker يعمل: `php artisan queue:work`
2. تحقق من Logs: `storage/logs/laravel.log`
3. تحقق من الـ Middleware مسجل صح
4. جرب بدون Queue (للاختبار فقط):

   ```php
   // في Middleware
   RecordPageVisitJob::dispatchSync($visitData);
   ```

### المشكلة: الإحصائيات لا تظهر

**الحل:**

1. تحقق من وجود بيانات: `SELECT COUNT(*) FROM page_visits`
2. امسح الـ Cache: `php artisan cache:clear`
3. تحقق من Filament مثبت ومشتغل

### المشكلة: الأداء بطيء

**الحل:**

1. استخدم Redis للـ Queue بدلاً من Database
2. أضف Indexes إضافية حسب الحاجة
3. استخدم `visits_summary` بدلاً من الاستعلامات المباشرة
4. قلل من TTL الـ Cache

---

## 📞 الأوامر السريعة

```bash
# عرض الإحصائيات
php artisan visits:stats

# تنظيف الزيارات القديمة (معاينة)
php artisan visits:clean --days=90 --dry-run

# تنظيف الزيارات القديمة (تنفيذ)
php artisan visits:clean --days=90

# تحديث ملخص أمس
php artisan visits:summarize

# تحديث ملخص تاريخ محدد
php artisan visits:summarize --date=2026-01-15

# تشغيل Queue Worker
php artisan queue:work --tries=3 --timeout=30

# عرض Queue Jobs
php artisan queue:failed
```

---

**تاريخ الإنشاء:** 2026-01-29  
**النسخة:** 2.0 (المحسّنة)  
**الحالة:** جاهز للتنفيذ ✅  
**التحسينات:** Queue Jobs, Bot Detection, Session Tracking, Performance Optimization, Summary Tables
