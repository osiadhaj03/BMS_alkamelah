# ============================================
# خطوات إعادة البناء الكاملة - البسيطة والقوية
# ============================================

## المرحلة 1: تطبيق Template البسيط

### الخطوة 1: حذف Template القديم (اختياري)
```powershell
Invoke-RestMethod -Uri "http://145.223.98.97:9201/_index_template/pages_enhanced_template" -Method Delete
```

### الخطوة 2: تطبيق Template الجديد البسيط
```powershell
$template = Get-Content "C:\Users\osaid\Documents\BMS_alkamelah\elasticsearch_simple_template.json" -Raw
Invoke-RestMethod -Uri "http://145.223.98.97:9201/_index_template/pages_simple_template" `
  -Method Put `
  -Body $template `
  -ContentType "application/json"
```

**النتيجة المتوقعة**: `{"acknowledged": true}`

### الخطوة 3: إنشاء Index جديد
```powershell
Invoke-RestMethod -Uri "http://145.223.98.97:9201/pages_simple_v1" -Method Put
```

**النتيجة**: `{"acknowledged": true, "shards_acknowledged": true, "index": "pages_simple_v1"}`

---

## المرحلة 2: تعديل Logstash Pipeline

### الخطوة 4: تعديل ملف Pipeline

**الملف**: `BMS_v1-homev2/logstash-setup/config/pipeline/bms-arabic-pages.conf`

```ruby
input {
  jdbc {
    jdbc_driver_library => "/usr/share/logstash/mysql-connector.jar"
    jdbc_driver_class => "com.mysql.cj.jdbc.Driver"
    jdbc_connection_string => "jdbc:mysql://145.223.98.97:3306/bms_v2?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC"
    jdbc_user => "bms_v2"
    jdbc_password => "bmsv2"
    
    # Schedule: every 30 seconds
    schedule => "*/30 * * * * *"
    
    # Tracking
    use_column_value => true
    tracking_column => "id"
    tracking_column_type => "numeric"
    last_run_metadata_path => "/usr/share/logstash/.logstash_jdbc_last_run_pages_simple"
    
    # SQL Query - SIMPLE! بدون author
    statement => "
      SELECT 
        p.id,
        p.page_number,
        p.content,
        p.book_id,
        b.title AS book_title,
        bs.name AS book_section,
        bs.id AS book_section_id
      FROM pages p
      LEFT JOIN books b ON p.book_id = b.id
      LEFT JOIN book_sections bs ON b.section_id = bs.id
      WHERE p.id > :sql_last_value
      ORDER BY p.id ASC
      LIMIT 5000
    "
    
    clean_run => false
    lowercase_column_names => true
  }
}

filter {
  mutate {
    convert => {
      "id" => "integer"
      "page_number" => "integer"
      "book_id" => "integer"
      "book_section_id" => "integer"
    }
  }
  
  ruby {
    code => "event.set('@timestamp', Time.now)"
  }
}

output {
  elasticsearch {
    hosts => ["http://145.223.98.97:9201"]
    index => "pages_simple_v1"
    document_id => "%{id}"
    doc_as_upsert => true
  }
  
  stdout {
    codec => dots
  }
}
```

### الخطوة 5: إعادة تشغيل Logstash

```powershell
cd C:\Users\osaid\Documents\BMS_alkamelah\BMS_v1-homev2\logstash-setup

# إيقاف الحاوية القديمة
docker-compose down

# حذف metadata file (للبدء من الصفر)
# اختياري - إذا بدك تبدأ من أول ID
# docker exec bms_logstash_arabic rm /usr/share/logstash/.logstash_jdbc_last_run_pages_simple

# تشغيل من جديد
docker-compose up -d

# مراقبة Logs
docker logs -f bms_logstash_arabic
```

---

## المرحلة 3: تعديل Laravel Code

### الخطوة 6: تبسيط UltraFastSearchService.php

**نوعين بحث فقط في البداية**:
1. **Match** - بحث بسيط (default)
2. **Wildcard** - بحث شامل (fallback)

```php
protected function buildOptimizedQuery(string $query, array $filters): array
{
    $boolQuery = [
        'bool' => [
            'must' => [],
            'filter' => [],
        ]
    ];

    if (!empty($query)) {
        // نوع واحد فقط: Match بسيط
        $operator = ($filters['word_match'] ?? 'some_words') === 'all_words' ? 'and' : 'or';
        
        $boolQuery['bool']['must'][] = [
            'match' => [
                'content' => [
                    'query' => $query,
                    'operator' => $operator
                ]
            ]
        ];
    }

    // Filters
    if (!empty($filters['book_id'])) {
        $bookIds = is_array($filters['book_id']) ? $filters['book_id'] : [$filters['book_id']];
        $boolQuery['bool']['filter'][] = ['terms' => ['book_id' => $bookIds]];
    }

    if (!empty($filters['section_id'])) {
        $sectionIds = is_array($filters['section_id']) ? $filters['section_id'] : [$filters['section_id']];
        $boolQuery['bool']['filter'][] = ['terms' => ['book_section_id' => $sectionIds]];
    }

    return $boolQuery;
}
```

### الخطوة 7: تحديث Index Name

```php
public function __construct()
{
    $this->elasticsearch = ClientBuilder::create()
        ->setHosts([config('services.elasticsearch.host', 'http://145.223.98.97:9201')])
        // ...
        ->build();
}

public function search(string $query, array $filters = [], int $page = 1, int $perPage = 15): array
{
    // ...
    $indexToUse = 'pages_simple_v1';  // ← استخدم Index الجديد
    // ...
}
```

---

## المرحلة 4: الاختبار

### الخطوة 8: اختبار مباشر على Elasticsearch

```powershell
# انتظر 2-3 دقائق بعد تشغيل Logstash

# اختبار 1: عد الصفحات
$count = Invoke-RestMethod -Uri "http://145.223.98.97:9201/pages_simple_v1/_count" -Method Get
Write-Host "Total documents: $($count.count)" -ForegroundColor Green

# اختبار 2: عينة من البيانات
$body = @{size=1; query=@{match_all=@{}}} | ConvertTo-Json
$sample = Invoke-RestMethod -Uri "http://145.223.98.97:9201/pages_simple_v1/_search" -Method Post -Body $body -ContentType "application/json"
$sample.hits.hits[0]._source | ConvertTo-Json -Depth 3

# اختبار 3: البحث البسيط
$queries = @("المكتبة", "الحمدلله", "رب العالمين", "الله", "محمد")
foreach($q in $queries) {
    $body = @{query=@{match=@{content=$q}}; size=0} | ConvertTo-Json -Depth 5
    $result = Invoke-RestMethod -Uri "http://145.223.98.97:9201/pages_simple_v1/_search" -Method Post -Body $body -ContentType "application/json"
    Write-Host "Query: '$q' -> Results: $($result.hits.total.value)" -ForegroundColor $(if($result.hits.total.value -gt 0){"Green"}else{"Red"})
}
```

### الخطوة 9: اختبار من Laravel

```powershell
# بعد تعديل الكود
cd C:\Users\osaid\Documents\BMS_alkamelah
php artisan config:cache
php artisan route:cache

# اختبار محلي (إذا في server محلي)
# أو Deploy للسيرفر الحقيقي
```

---

## المرحلة 5: Alias للانتقال السلس

### الخطوة 10: إنشاء Alias

```powershell
# بعد التأكد أن كل شي يشتغل 100%
$body = @{
    actions = @(
        @{
            remove = @{
                index = "pages_v3"
                alias = "pages_active"
            }
        },
        @{
            add = @{
                index = "pages_simple_v1"
                alias = "pages_active"
            }
        }
    )
} | ConvertTo-Json -Depth 5

Invoke-RestMethod -Uri "http://145.223.98.97:9201/_aliases" `
  -Method Post `
  -Body $body `
  -ContentType "application/json"
```

**الآن**: `pages_active` يشير إلى `pages_simple_v1` ✅

---

## ملخص الفروقات

| **الميزة** | **القديم (pages_v3)** | **الجديد (pages_simple_v1)** |
|------------|---------------------|------------------------|
| **Analyzer** | `arabic_enhanced` معقد | `arabic_simple` بسيط |
| **Stopwords** | ✅ يحذف "ال", "في", "من" | ❌ بدون stopwords |
| **Stemmer** | ✅ arabic_stemmer | ❌ بدون stemming |
| **Search Analyzer** | `arabic_search` مختلف | `arabic_search_simple` نفسه |
| **Fields** | multi-fields معقدة | field واحد بسيط |
| **Author** | حاولنا نضيفه | ❌ بدون author (للتبسيط) |
| **SQL Query** | معقد مع subquery | بسيط مع LEFT JOIN |

---

## لماذا هذا أفضل؟

### ✅ **المزايا**:

1. **Analyzer واحد**: `arabic_simple` = `arabic_search_simple`
   - لا يوجد mismatch بين indexing و searching
   
2. **بدون Stopwords**: 
   - "المكتبة" → ["ال", "مكتبه"] 
   - كل الكلمات تُحفظ!

3. **بدون Stemming**:
   - "كتاب" = "كتاب" (كما هي)
   - مش "كتب" أو "كتابة"

4. **Character Normalization فقط**:
   - ة → ه
   - أ, إ, آ → ا
   - ى → ي
   - بسيط وفعّال!

5. **SQL بسيط**:
   - بدون GROUP BY
   - بدون subqueries
   - بدون author (نضيفه لاحقاً)

---

## التوقيت المتوقع

- **تطبيق Template**: 10 ثواني
- **تعديل Logstash**: 5 دقائق
- **Indexing 5.3M pages**: 2-3 ساعات
- **تعديل Laravel Code**: 15 دقيقة
- **الاختبار**: 10 دقائق

**الإجمالي**: ~3 ساعات (معظمها indexing)

---

## الخطوة التالية بعد النجاح

بعد ما تتأكد أن البحث البسيط يشتغل 100%، نضيف:

1. **Stemming** (اختياري - للبحث الصرفي)
2. **Synonyms** (مثلاً: الله = رب = المولى)
3. **Author field** (نضيف بعدين)
4. **NGram** (للـ autocomplete)
5. **Fuzzy matching** (للأخطاء الإملائية)

لكن **الأولوية**: بحث بسيط يشتغل! 🎯
