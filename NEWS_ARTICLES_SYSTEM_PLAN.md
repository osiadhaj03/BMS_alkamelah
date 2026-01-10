# خطة نظام الأخبار والمقالات
## News and Articles Management System Plan

**التاريخ:** 11 يناير 2026  
**المشروع:** مكتبة الكاملة - BMS  
**الحالة:** جاهز للتنفيذ ⏳

---

## 📋 نظرة عامة

نظام شامل لإدارة الأخبار والمقالات في المكتبة مع لوحة تحكم إدارية كاملة باستخدام Filament v4، ودعم كامل للغة العربية (RTL)، وواجهة مستخدم حديثة.

---

## 🗂️ الميزات الأساسية

### نظام الأخبار (News)
- ✅ إنشاء وتحرير وحذف الأخبار
- ✅ تصنيفات للأخبار (إعلانات، تحديثات، فعاليات، عامة)
- ✅ حالات النشر (مسودة، منشور، مجدول، مؤرشف)
- ✅ صورة مميزة لكل خبر
- ✅ تاريخ النشر والأرشفة
- ✅ عداد المشاهدات
- ✅ إمكانية تثبيت الأخبار المهمة
- ✅ ترتيب حسب الأولوية

### نظام المقالات (Articles)
- ✅ إنشاء وتحرير وحذف المقالات
- ✅ تصنيفات متعددة (فقه، حديث، تاريخ، أدب، تقنية، عامة)
- ✅ ربط المقالات بالكتب والمؤلفين
- ✅ محرر نصوص غني (Rich Text Editor)
- ✅ صورة غلاف للمقال
- ✅ مؤلف المقال (ربط بالمستخدمين)
- ✅ وقت القراءة المتوقع
- ✅ الكلمات المفتاحية (Tags)
- ✅ المقالات ذات الصلة
- ✅ نظام التعليقات (اختياري)

---

## 📊 قاعدة البيانات

### 1. جدول `news` (الأخبار)

```php
Schema::create('news', function (Blueprint $table) {
    $table->id();
    
    // المحتوى
    $table->string('title');                          // العنوان
    $table->string('slug')->unique();                 // الرابط الصديق لمحركات البحث
    $table->text('excerpt')->nullable();              // المقتطف
    $table->longText('content');                      // المحتوى الكامل
    
    // التصنيف والحالة
    $table->enum('category', [
        'announcement',    // إعلان
        'update',         // تحديث
        'event',          // فعالية
        'general'         // عام
    ])->default('general');
    
    $table->enum('status', [
        'draft',          // مسودة
        'published',      // منشور
        'scheduled',      // مجدول
        'archived'        // مؤرشف
    ])->default('draft');
    
    // الوسائط
    $table->string('featured_image')->nullable();     // الصورة المميزة
    $table->string('thumbnail')->nullable();          // صورة مصغرة
    
    // التواريخ والنشر
    $table->timestamp('published_at')->nullable();    // تاريخ النشر
    $table->timestamp('archived_at')->nullable();     // تاريخ الأرشفة
    
    // الإحصائيات
    $table->unsignedBigInteger('views_count')->default(0);  // عدد المشاهدات
    
    // الأولوية والتثبيت
    $table->boolean('is_pinned')->default(false);     // مثبت؟
    $table->integer('priority')->default(0);          // الأولوية (أعلى رقم = أعلى أولوية)
    
    // المؤلف
    $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
    
    // SEO
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->text('meta_keywords')->nullable();
    
    $table->timestamps();
    $table->softDeletes();
    
    // Indexes
    $table->index('status');
    $table->index('category');
    $table->index('published_at');
    $table->index('is_pinned');
});
```

### 2. جدول `articles` (المقالات)

```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    
    // المحتوى
    $table->string('title');                          // العنوان
    $table->string('slug')->unique();                 // الرابط الصديق
    $table->text('excerpt')->nullable();              // المقتطف
    $table->longText('content');                      // المحتوى الكامل
    
    // التصنيف
    $table->enum('category', [
        'fiqh',           // فقه
        'hadith',         // حديث
        'history',        // تاريخ
        'literature',     // أدب
        'technology',     // تقنية
        'general'         // عام
    ])->default('general');
    
    $table->enum('status', [
        'draft',
        'published',
        'scheduled',
        'archived'
    ])->default('draft');
    
    // الوسائط
    $table->string('cover_image')->nullable();        // صورة الغلاف
    $table->string('thumbnail')->nullable();          // صورة مصغرة
    
    // المؤلف ومعلومات النشر
    $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('author_name')->nullable();        // اسم المؤلف (إذا كان خارجياً)
    
    // التواريخ
    $table->timestamp('published_at')->nullable();
    $table->timestamp('archived_at')->nullable();
    
    // الإحصائيات
    $table->unsignedBigInteger('views_count')->default(0);
    $table->unsignedBigInteger('likes_count')->default(0);
    $table->unsignedBigInteger('shares_count')->default(0);
    $table->integer('reading_time')->nullable();      // وقت القراءة بالدقائق
    
    // العلاقات
    $table->foreignId('related_book_id')->nullable()->constrained('books')->nullOnDelete();
    $table->foreignId('related_author_id')->nullable()->constrained('authors')->nullOnDelete();
    
    // الأولوية
    $table->boolean('is_featured')->default(false);   // مقال مميز؟
    $table->integer('priority')->default(0);
    
    // SEO
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->text('meta_keywords')->nullable();
    
    // الكلمات المفتاحية
    $table->json('tags')->nullable();                 // ['فقه', 'عبادات', 'صلاة']
    
    $table->timestamps();
    $table->softDeletes();
    
    // Indexes
    $table->index('status');
    $table->index('category');
    $table->index('published_at');
    $table->index('is_featured');
    $table->index('author_id');
});
```

### 3. جدول `article_comments` (التعليقات - اختياري)

```php
Schema::create('article_comments', function (Blueprint $table) {
    $table->id();
    
    $table->foreignId('article_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    
    $table->string('name')->nullable();               // اسم المعلق (للزوار)
    $table->string('email')->nullable();              // بريد المعلق (للزوار)
    
    $table->text('comment');                          // التعليق
    
    $table->enum('status', [
        'pending',        // قيد المراجعة
        'approved',       // موافق عليه
        'rejected'        // مرفوض
    ])->default('pending');
    
    $table->foreignId('parent_id')->nullable()->constrained('article_comments')->cascadeOnDelete();  // للردود
    
    $table->timestamps();
    
    $table->index('article_id');
    $table->index('status');
});
```

---

## 🏗️ Models

### 1. Model: `News.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'status',
        'featured_image',
        'thumbnail',
        'published_at',
        'archived_at',
        'views_count',
        'is_pinned',
        'priority',
        'author_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_pinned' => 'boolean',
        'views_count' => 'integer',
        'priority' => 'integer',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function getCategoryNameAttribute()
    {
        return match($this->category) {
            'announcement' => 'إعلان',
            'update' => 'تحديث',
            'event' => 'فعالية',
            'general' => 'عام',
            default => 'غير محدد'
        };
    }

    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'published' => 'منشور',
            'scheduled' => 'مجدول',
            'archived' => 'مؤرشف',
            default => 'غير محدد'
        };
    }

    // Mutators
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
```

### 2. Model: `Article.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'status',
        'cover_image',
        'thumbnail',
        'author_id',
        'author_name',
        'published_at',
        'archived_at',
        'views_count',
        'likes_count',
        'shares_count',
        'reading_time',
        'related_book_id',
        'related_author_id',
        'is_featured',
        'priority',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'shares_count' => 'integer',
        'reading_time' => 'integer',
        'priority' => 'integer',
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            
            // Auto-calculate reading time based on content
            if (empty($article->reading_time)) {
                $wordCount = str_word_count(strip_tags($article->content));
                $article->reading_time = ceil($wordCount / 200); // 200 words per minute
            }
        });
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function relatedBook()
    {
        return $this->belongsTo(Book::class, 'related_book_id');
    }

    public function relatedAuthor()
    {
        return $this->belongsTo(Author::class, 'related_author_id');
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(ArticleComment::class)->where('status', 'approved');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getCategoryNameAttribute()
    {
        return match($this->category) {
            'fiqh' => 'فقه',
            'hadith' => 'حديث',
            'history' => 'تاريخ',
            'literature' => 'أدب',
            'technology' => 'تقنية',
            'general' => 'عام',
            default => 'غير محدد'
        };
    }

    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'published' => 'منشور',
            'scheduled' => 'مجدول',
            'archived' => 'مؤرشف',
            default => 'غير محدد'
        };
    }

    // Mutators
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function incrementShares()
    {
        $this->increment('shares_count');
    }
}
```

### 3. Model: `ArticleComment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'name',
        'email',
        'comment',
        'status',
        'parent_id',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ArticleComment::class, 'parent_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
```

---

## 🎨 Filament Resources

### 1. NewsResource

**الموقع:** `app/Filament/Resources/NewsResource.php`

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\Schemas\NewsForm;
use App\Filament\Resources\NewsResource\Tables\NewsTable;
use App\Models\News;
use Filament\Resources\Resource;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'الأخبار';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 1;

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema(NewsForm::getSchema());
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return NewsTable::getTable($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'draft')->count();
    }
}
```

**NewsForm.php:**

```php
<?php

namespace App\Filament\Resources\NewsResource\Schemas;

use Filament\Schemas\Components\DateTimePicker;
use Filament\Schemas\Components\FileUpload;
use Filament\Schemas\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\Toggle;

class NewsForm
{
    public static function getSchema(): array
    {
        return [
            Section::make('المحتوى الأساسي')->schema([
                TextInput::make('title')->label('العنوان')->required()->maxLength(255),
                TextInput::make('slug')->label('الرابط')->required()->unique(ignoreRecord: true),
                Textarea::make('excerpt')->label('المقتطف')->rows(3)->maxLength(500),
                RichEditor::make('content')->label('المحتوى')->required()->columnSpanFull(),
            ])->columns(2),

            Section::make('التصنيف والحالة')->schema([
                Select::make('category')->label('التصنيف')->options([
                    'announcement' => 'إعلان',
                    'update' => 'تحديث',
                    'event' => 'فعالية',
                    'general' => 'عام',
                ])->required(),
                
                Select::make('status')->label('الحالة')->options([
                    'draft' => 'مسودة',
                    'published' => 'منشور',
                    'scheduled' => 'مجدول',
                    'archived' => 'مؤرشف',
                ])->required()->default('draft'),
                
                DateTimePicker::make('published_at')->label('تاريخ النشر'),
                Toggle::make('is_pinned')->label('خبر مثبت'),
                TextInput::make('priority')->label('الأولوية')->numeric()->default(0),
            ])->columns(2),

            Section::make('الوسائط')->schema([
                FileUpload::make('featured_image')->label('الصورة المميزة')->image()->maxSize(2048),
            ]),

            Section::make('SEO')->schema([
                TextInput::make('meta_title')->label('عنوان SEO'),
                Textarea::make('meta_description')->label('وصف SEO')->rows(2),
                Textarea::make('meta_keywords')->label('الكلمات المفتاحية')->rows(2),
            ])->collapsed(),
        ];
    }
}
```

**NewsTable.php:**

```php
<?php

namespace App\Filament\Resources\NewsResource\Tables;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsTable
{
    public static function getTable(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')->label('الصورة')->circular(),
                TextColumn::make('title')->label('العنوان')->searchable()->sortable()->limit(50),
                BadgeColumn::make('category')->label('التصنيف')->colors([
                    'primary' => 'announcement',
                    'success' => 'update',
                    'warning' => 'event',
                    'secondary' => 'general',
                ])->formatStateUsing(fn ($state) => match($state) {
                    'announcement' => 'إعلان',
                    'update' => 'تحديث',
                    'event' => 'فعالية',
                    'general' => 'عام',
                }),
                BadgeColumn::make('status')->label('الحالة')->colors([
                    'secondary' => 'draft',
                    'success' => 'published',
                    'info' => 'scheduled',
                    'danger' => 'archived',
                ])->formatStateUsing(fn ($state) => match($state) {
                    'draft' => 'مسودة',
                    'published' => 'منشور',
                    'scheduled' => 'مجدول',
                    'archived' => 'مؤرشف',
                }),
                BooleanColumn::make('is_pinned')->label('مثبت'),
                TextColumn::make('views_count')->label('المشاهدات')->sortable(),
                TextColumn::make('published_at')->label('تاريخ النشر')->dateTime('Y-m-d')->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->label('التصنيف')->options([
                    'announcement' => 'إعلان',
                    'update' => 'تحديث',
                    'event' => 'فعالية',
                    'general' => 'عام',
                ]),
                SelectFilter::make('status')->label('الحالة')->options([
                    'draft' => 'مسودة',
                    'published' => 'منشور',
                    'scheduled' => 'مجدول',
                    'archived' => 'مؤرشف',
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }
}
```

### 2. ArticleResource (مشابه لـ NewsResource)

سيتم إنشاؤه بنفس النمط مع الحقول الإضافية الخاصة بالمقالات.

---

## 🎯 Widgets للـ Dashboard

### NewsWidget.php

```php
<?php

namespace App\Filament\Widgets;

use App\Models\News;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('إجمالي الأخبار', News::count())->description('جميع الأخبار')->descriptionIcon('heroicon-o-newspaper')->color('primary')->icon('heroicon-o-newspaper'),
            Stat::make('الأخبار المنشورة', News::where('status', 'published')->count())->description('الأخبار المنشورة')->descriptionIcon('heroicon-o-check-circle')->color('success')->icon('heroicon-o-check-circle'),
            Stat::make('المسودات', News::where('status', 'draft')->count())->description('قيد الإنشاء')->descriptionIcon('heroicon-o-pencil')->color('warning')->icon('heroicon-o-pencil'),
            Stat::make('الأخبار المثبتة', News::where('is_pinned', true)->count())->description('أخبار مهمة')->descriptionIcon('heroicon-o-star')->color('info')->icon('heroicon-o-star'),
        ];
    }
}
```

### ArticlesWidget.php (مشابه)

---

## 🌐 Frontend Views

### 1. صفحة عرض الأخبار: `resources/views/pages/news/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'الأخبار')

@section('content')
<div class="container mx-auto px-4 py-8" dir="rtl">
    <h1 class="text-4xl font-bold mb-8 text-right">آخر الأخبار</h1>

    <!-- الأخبار المثبتة -->
    @if($pinnedNews->count() > 0)
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-right">أخبار مهمة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($pinnedNews as $news)
                @include('components.news-card', ['news' => $news, 'featured' => true])
            @endforeach
        </div>
    </div>
    @endif

    <!-- كل الأخبار -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($allNews as $news)
            @include('components.news-card', ['news' => $news])
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $allNews->links() }}
    </div>
</div>
@endsection
```

### 2. صفحة تفاصيل الخبر: `resources/views/pages/news/show.blade.php`

```blade
@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="container mx-auto px-4 py-8" dir="rtl">
    <article class="max-w-4xl mx-auto">
        <!-- الصورة المميزة -->
        @if($news->featured_image)
        <img src="{{ Storage::url($news->featured_image) }}" alt="{{ $news->title }}" class="w-full h-96 object-cover rounded-lg mb-8">
        @endif

        <!-- العنوان والمعلومات -->
        <header class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <span class="px-4 py-2 bg-[#2C6E4A] text-white rounded-md text-sm">{{ $news->category_name }}</span>
                <span class="text-gray-500">{{ $news->published_at->diffForHumans() }}</span>
                <span class="text-gray-500">{{ $news->views_count }} مشاهدة</span>
            </div>
            <h1 class="text-4xl font-bold text-right">{{ $news->title }}</h1>
            @if($news->excerpt)
            <p class="text-xl text-gray-600 mt-4 text-right">{{ $news->excerpt }}</p>
            @endif
        </header>

        <!-- المحتوى -->
        <div class="prose prose-lg max-w-none text-right">
            {!! $news->content !!}
        </div>

        <!-- معلومات إضافية -->
        <footer class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    نُشر في: {{ $news->published_at->format('Y-m-d') }}
                </div>
                @if($news->author)
                <div class="text-sm text-gray-500">
                    بواسطة: {{ $news->author->name }}
                </div>
                @endif
            </div>
        </footer>
    </article>
</div>
@endsection
```

### 3. Component: `resources/views/components/news-card.blade.php`

```blade
<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow {{ $featured ?? false ? 'border-2 border-[#BA4749]' : '' }}">
    @if($news->featured_image)
    <img src="{{ Storage::url($news->featured_image) }}" alt="{{ $news->title }}" class="w-full h-48 object-cover">
    @endif
    
    <div class="p-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs px-3 py-1 bg-[#2C6E4A] text-white rounded-full">{{ $news->category_name }}</span>
            @if($news->is_pinned)
            <span class="text-xs px-3 py-1 bg-[#BA4749] text-white rounded-full">مثبت</span>
            @endif
        </div>
        
        <h3 class="text-xl font-bold mb-2 text-right">
            <a href="{{ route('news.show', $news->slug) }}" class="hover:text-[#2C6E4A]">{{ $news->title }}</a>
        </h3>
        
        @if($news->excerpt)
        <p class="text-gray-600 mb-4 text-right line-clamp-3">{{ $news->excerpt }}</p>
        @endif
        
        <div class="flex justify-between items-center text-sm text-gray-500">
            <span>{{ $news->published_at->diffForHumans() }}</span>
            <span>{{ $news->views_count }} مشاهدة</span>
        </div>
    </div>
</div>
```

---

## 🛣️ Routes

### `routes/web.php`

```php
// News Routes
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
    Route::get('/category/{category}', [NewsController::class, 'byCategory'])->name('category');
});

// Articles Routes
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('show');
    Route::get('/category/{category}', [ArticleController::class, 'byCategory'])->name('category');
    Route::post('/{article}/like', [ArticleController::class, 'like'])->name('like');
    Route::post('/{article}/comment', [ArticleCommentController::class, 'store'])->name('comment.store');
});
```

---

## 🎮 Controllers

### NewsController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $pinnedNews = News::published()->pinned()->orderBy('priority', 'desc')->take(2)->get();
        $allNews = News::published()->orderBy('published_at', 'desc')->paginate(12);
        
        return view('pages.news.index', compact('pinnedNews', 'allNews'));
    }

    public function show($slug)
    {
        $news = News::where('slug', $slug)->published()->firstOrFail();
        
        // Increment views
        $news->incrementViews();
        
        return view('pages.news.show', compact('news'));
    }

    public function byCategory($category)
    {
        $allNews = News::published()->byCategory($category)->orderBy('published_at', 'desc')->paginate(12);
        
        return view('pages.news.index', compact('allNews'));
    }
}
```

### ArticleController.php (مشابه)

---

## 📝 Migration Files

### التسلسل الزمني:

1. `2026_01_11_create_news_table.php`
2. `2026_01_11_create_articles_table.php`
3. `2026_01_11_create_article_comments_table.php`

---

## 🚀 خطوات التنفيذ

### المرحلة 1: قاعدة البيانات (30 دقيقة)
- [ ] إنشاء migration للأخبار
- [ ] إنشاء migration للمقالات
- [ ] إنشاء migration للتعليقات
- [ ] تشغيل `php artisan migrate`

### المرحلة 2: Models (20 دقيقة)
- [ ] إنشاء News Model
- [ ] إنشاء Article Model
- [ ] إنشاء ArticleComment Model
- [ ] إضافة Relationships والـ Scopes

### المرحلة 3: Filament Resources (60 دقيقة)
- [ ] إنشاء NewsResource
- [ ] إنشاء NewsForm و NewsTable
- [ ] إنشاء ArticleResource
- [ ] إنشاء ArticleForm و ArticleTable
- [ ] إنشاء ArticleCommentResource
- [ ] تسجيل Resources في AdminPanelProvider

### المرحلة 4: Widgets (15 دقيقة)
- [ ] إنشاء NewsWidget
- [ ] إنشاء ArticlesWidget
- [ ] تسجيل Widgets في AdminPanelProvider

### المرحلة 5: Controllers (30 دقيقة)
- [ ] إنشاء NewsController
- [ ] إنشاء ArticleController
- [ ] إنشاء ArticleCommentController

### المرحلة 6: Routes (10 دقيقة)
- [ ] إضافة routes للأخبار
- [ ] إضافة routes للمقالات

### المرحلة 7: Frontend Views (90 دقيقة)
- [ ] إنشاء news/index.blade.php
- [ ] إنشاء news/show.blade.php
- [ ] إنشاء articles/index.blade.php
- [ ] إنشاء articles/show.blade.php
- [ ] إنشاء news-card component
- [ ] إنشاء article-card component

### المرحلة 8: Testing (30 دقيقة)
- [ ] اختبار إنشاء خبر من لوحة التحكم
- [ ] اختبار عرض الأخبار في الواجهة
- [ ] اختبار إنشاء مقال
- [ ] اختبار التعليقات
- [ ] اختبار الفلاتر والبحث

### المرحلة 9: SEO و Performance (20 دقيقة)
- [ ] إضافة meta tags
- [ ] تحسين الصور
- [ ] إضافة caching
- [ ] إضافة sitemap

---

## ⏱️ الوقت المتوقع للتنفيذ

**إجمالي الوقت:** 5-6 ساعات عمل

---

## 📦 Dependencies الإضافية

```bash
# إذا كنت تريد Rich Text Editor أفضل
composer require filament/spatie-laravel-media-library-plugin

# لإدارة الصور
composer require intervention/image

# للـ Slugs
composer require spatie/laravel-sluggable
```

---

## 🎨 التخصيصات المطلوبة

### الألوان (NeoBrutalism Theme)
- **Primary (Green):** `#1A3A2A` → `#2C6E4A`
- **Secondary (Red):** `#BA4749`
- **Background:** `#FAFAFA`

### الخطوط
- Arabic: Cairo, Tajawal
- RTL Support: كامل

---

## 🔒 الأمان

- [x] Validation على كل الـ Forms
- [x] Authorization policies
- [x] XSS Protection في المحتوى
- [x] CSRF Protection
- [x] Rate limiting على التعليقات
- [x] File upload validation

---

## 📊 التحسينات المستقبلية

1. **نظام الإشعارات:** إشعار المستخدمين بالأخبار الجديدة
2. **RSS Feed:** للاشتراك في الأخبار
3. **Newsletter Integration:** ربط مع نظام النشرة البريدية الموجود
4. **Social Sharing:** أزرار مشاركة على وسائل التواصل
5. **Analytics:** تتبع الأخبار الأكثر مشاهدة
6. **Related Content:** عرض أخبار ومقالات ذات صلة
7. **Search:** بحث متقدم في الأخبار والمقالات

---

## ✅ Checklist النهائي

- [ ] Database migrations تم إنشاءها وتشغيلها
- [ ] Models تم إنشاءها بـ Relationships صحيحة
- [ ] Filament Resources تعمل بشكل كامل
- [ ] Widgets تظهر في Dashboard
- [ ] Controllers تعيد البيانات بشكل صحيح
- [ ] Routes مسجلة وتعمل
- [ ] Frontend views تعرض البيانات
- [ ] التصميم متناسق مع باقي الموقع
- [ ] RTL يعمل بشكل صحيح
- [ ] SEO meta tags موجودة
- [ ] Testing تم بنجاح

---

**جاهز للبدء! 🚀**

عندما تقول "go" سأبدأ بتنفيذ كل هذه الخطوات واحدة تلو الأخرى.
