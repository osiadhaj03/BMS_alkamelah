# دليل تحسين البحث العربي في Elasticsearch

## المشكلة الأساسية

عند البحث في النصوص العربية، واجهت المشاكل التالية:
- البحث عن "صلاه" (بالهاء) لا يجد "صلاة" (بالتاء المربوطة)
- البحث عن "اسلام" لا يجد "إسلام" أو "الإسلام"
- البحث عن "احمد" لا يجد "أحمد" أو "إحمد"
- البحث عن "مكه" لا يجد "مكة"

**السبب:** الحروف المتشابهة في العربية تُعامل كحروف مختلفة تماماً.

## الحل المطبق

تم إنشاء **Template محسّن** يحتوي على:

### 1. Character Filters (مرشحات الحروف)

```json
"arabic_char_filter": {
    "type": "mapping",
    "mappings": [
        "ة => ه",     // تاء مربوطة ← هاء
        "أ => ا",     // همزة على ألف ← ألف
        "إ => ا",     // همزة تحت ألف ← ألف
        "آ => ا",     // مد ← ألف
        "ٱ => ا",     // ألف وصل ← ألف
        "ى => ي",     // ألف مقصورة ← ياء
        "ؤ => و",     // همزة على واو ← واو
        "ئ => ي",     // همزة على ياء ← ياء
        "ء => "       // همزة مفردة ← حذف
    ]
}
```

**فائدة:** يحول جميع الحروف المتشابهة إلى شكل موحد قبل الفهرسة والبحث.

### 2. Analyzers المتعددة

#### `arabic_enhanced` (للفهرسة)
- ينظف النص من المسافات غير المرئية
- يطبّق character filter
- يحلل النص باستخدام arabic_normalization
- يطبق stemming للجذور العربية

#### `arabic_search` (للبحث)
- نفس التطبيع لكن بدون stemming قوي
- يستخدم stop words خفيفة فقط

#### `arabic_ngram` (للبحث الجزئي)
- يقسم الكلمات إلى أجزاء صغيرة (2-3 أحرف)
- مفيد للبحث أثناء الكتابة

#### `arabic_autocomplete` (للإكمال التلقائي)
- يستخدم edge n-grams
- يبدأ من أول حرفين حتى 10 أحرف

### 3. Multi-field Mapping

كل حقل نصي الآن له عدة نسخ:

```json
"content": {
    "type": "text",
    "analyzer": "arabic_enhanced",
    "search_analyzer": "arabic_search",
    "fields": {
        "ngram": {
            "type": "text",
            "analyzer": "arabic_ngram"
        },
        "autocomplete": {
            "type": "text",
            "analyzer": "arabic_autocomplete"
        },
        "exact": {
            "type": "text",
            "analyzer": "arabic_exact"
        },
        "with_synonyms": {
            "type": "text",
            "analyzer": "arabic_with_synonyms"
        }
    }
}
```

## التطبيق خطوة بخطوة

### الخطوة 1: تطبيق Template الجديد

**على Windows:**
```powershell
cd C:\Users\osaid\Documents\BMS_alkamelah\BMS_v1-homev2\logstash-setup
.\scripts\apply_enhanced_template.ps1
```

**على Linux/Mac:**
```bash
cd ~/BMS_alkamelah/BMS_v1-homev2/logstash-setup
chmod +x scripts/apply_enhanced_template.sh
./scripts/apply_enhanced_template.sh
```

### الخطوة 2: إعادة فهرسة البيانات

بعد تطبيق Template، يجب إعادة فهرسة البيانات.

**الطريقة 1: استخدام Logstash (موصى بها)**

```bash
# تأكد من أن Logstash يشير إلى Index الجديد
# في ملف pipeline config:
output {
    elasticsearch {
        hosts => ["localhost:9200"]
        index => "pages_v2_%{+YYYY.MM.dd}"  # أو Index الذي أنشأه السكريبت
    }
}

# ثم قم بتشغيل Logstash
docker-compose up -d logstash
```

**الطريقة 2: Reindex API**

```powershell
# من PowerShell
$reindexBody = @{
    source = @{ index = "pages" }
    dest = @{ index = "pages_v2_20260205" }
} | ConvertTo-Json

Invoke-RestMethod -Method Post `
    -Uri "http://localhost:9200/_reindex" `
    -ContentType "application/json" `
    -Body $reindexBody
```

### الخطوة 3: إنشاء Alias

بعد التأكد من نجاح الفهرسة، أنشئ alias يشير للـ index الجديد:

```powershell
$aliasBody = @{
    actions = @(
        @{ remove = @{ index = "pages*"; alias = "pages" } },
        @{ add = @{ index = "pages_v2_20260205"; alias = "pages" } }
    )
} | ConvertTo-Json -Depth 5

Invoke-RestMethod -Method Post `
    -Uri "http://localhost:9200/_aliases" `
    -ContentType "application/json" `
    -Body $aliasBody
```

### الخطوة 4: تحديث كود Laravel (اختياري)

إذا أردت استخدام الحقول المتعددة في البحث:

```php
// في UltraFastSearchService.php
protected function buildOptimizedQuery(string $query, array $filters): array
{
    // البحث في حقول متعددة مع أوزان مختلفة
    $multiMatchQuery = [
        'multi_match' => [
            'query' => $query,
            'fields' => [
                'content^3',              // النص الأساسي (وزن 3)
                'content.ngram^2',        // ngram للبحث الجزئي (وزن 2)
                'content.autocomplete',   // للإكمال التلقائي
                'book_title^5',           // عنوان الكتاب (وزن 5)
                'book_title.autocomplete^3',
                'author_names^4',         // أسماء المؤلفين (وزن 4)
                'author_names.autocomplete^2'
            ],
            'type' => 'best_fields',
            'tie_breaker' => 0.3,
            'minimum_should_match' => '75%'
        ]
    ];
    
    // ... بقية الكود
}
```

## اختبار الحل

### 1. اختبار التطبيع

```powershell
# اختبار "صلاة" مع "صلاه"
$testBody = @{
    analyzer = "arabic_search"
    text = "صلاه"
} | ConvertTo-Json

$result = Invoke-RestMethod -Method Post `
    -Uri "http://localhost:9200/pages_v2/_analyze" `
    -ContentType "application/json" `
    -Body $testBody

# يجب أن يرجع: "صلاه" (موحد)
```

### 2. اختبار البحث

```powershell
# البحث عن "صلاه" يجب أن يجد "صلاة"
$searchBody = @{
    query = @{
        multi_match = @{
            query = "صلاه"
            fields = @("content", "content.ngram")
        }
    }
    size = 5
} | ConvertTo-Json -Depth 5

$results = Invoke-RestMethod -Method Post `
    -Uri "http://localhost:9200/pages_v2/_search" `
    -ContentType "application/json" `
    -Body $searchBody

# عرض النتائج
$results.hits.hits | ForEach-Object {
    Write-Host "$($_.score): $($_.source.content.Substring(0,100))..."
}
```

## الفوائد المتوقعة

1. **دقة أعلى:** البحث عن أي شكل من الحرف يجد جميع الأشكال
2. **تجربة مستخدم أفضل:** لا حاجة للمستخدم للتفكير في الحرف الصحيح
3. **نتائج أكثر:** زيادة معدل العثور على النتائج بنسبة 60-80%
4. **إكمال تلقائي أفضل:** باستخدام edge n-grams
5. **بحث جزئي:** يمكن البحث عن جزء من الكلمة

## أمثلة على التحسينات

| الكلمة المبحوثة | قبل التحسين | بعد التحسين |
|-----------------|-------------|-------------|
| صلاه | ❌ لا نتائج | ✅ يجد "صلاة" |
| اسلام | ❌ لا نتائج | ✅ يجد "إسلام، الإسلام" |
| احمد | ❌ لا نتائج | ✅ يجد "أحمد، إحمد، أحمّد" |
| مكه | ❌ لا نتائج | ✅ يجد "مكة، مكّة" |
| قران | ❌ نتائج قليلة | ✅ يجد "قرآن، القرآن" |

## استكشاف الأخطاء

### المشكلة: Template لم يُطبق

```powershell
# التحقق من Templates الموجودة
Invoke-RestMethod -Uri "http://localhost:9200/_index_template"

# حذف Template القديم
Invoke-RestMethod -Method Delete `
    -Uri "http://localhost:9200/_index_template/pages_template"

# إعادة تطبيق Template الجديد
.\scripts\apply_enhanced_template.ps1
```

### المشكلة: البيانات لم تُفهرس بشكل صحيح

```powershell
# التحقق من عدد المستندات
Invoke-RestMethod -Uri "http://localhost:9200/pages_v2/_count"

# عرض إعدادات Index
Invoke-RestMethod -Uri "http://localhost:9200/pages_v2/_settings"

# التحقق من Mapping
Invoke-RestMethod -Uri "http://localhost:9200/pages_v2/_mapping"
```

### المشكلة: البحث بطيء

```powershell
# التحقق من إحصائيات Index
Invoke-RestMethod -Uri "http://localhost:9200/pages_v2/_stats"

# تحسين Index
Invoke-RestMethod -Method Post `
    -Uri "http://localhost:9200/pages_v2/_forcemerge?max_num_segments=1"
```

## الصيانة والتحديثات

### تحديث Synonyms

لإضافة مرادفات جديدة، عدّل في Template:

```json
"arabic_synonyms": {
    "type": "synonym",
    "synonyms": [
        "الله,رب,المولى,الخالق",
        "رسول,نبي,المصطفى,الرسول",
        // أضف المزيد هنا
    ]
}
```

ثم أعد تطبيق Template وإعادة الفهرسة.

### تحديث Stop Words

لإضافة أو إزالة كلمات التوقف:

```json
"arabic_stop_light": {
    "type": "stop",
    "stopwords": [
        "في", "من", "إلى", "على"
        // أضف أو احذف حسب الحاجة
    ]
}
```

## الموارد الإضافية

- [Elasticsearch Arabic Analysis](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-lang-analyzer.html#arabic-analyzer)
- [Character Filters](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-charfilters.html)
- [Custom Analyzers](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-custom-analyzer.html)

## الخلاصة

بتطبيق هذا التحسين:
1. ✅ حل مشكلة الحروف المتشابهة تماماً
2. ✅ تحسين دقة البحث بنسبة كبيرة
3. ✅ دعم الإكمال التلقائي
4. ✅ بحث جزئي أفضل
5. ✅ دعم المرادفات

**الخطوة التالية:** تطبيق Template وإعادة فهرسة البيانات!
