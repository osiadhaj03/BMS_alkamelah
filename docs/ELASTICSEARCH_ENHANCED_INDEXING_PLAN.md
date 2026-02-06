# خطة تنفيذ الفهرسة العربية المحسّنة - Elasticsearch

## 📋 ملخص المشروع

**الهدف:** إصلاح مشاكل البحث العربي في Elasticsearch وتحسين دقة البحث بنسبة 60-80%

**المشاكل الحالية:**

1. ❌ الحروف العربية غير موحدة (ة≠ه، آ≠ا، إ≠أ≠ا)
2. ❌ حقل `author_ids` غير موجود - فلتر المؤلفين لا يعمل
3. ❌ الحقول الفرعية (ngram, autocomplete, stemmed) غير مستخدمة

**الملفات الرئيسية:**

- Template: `BMS_v1-homev2/logstash-setup/elasticsearch/pages_enhanced_template.json`
- Pipeline: `BMS_v1-homev2/logstash-setup/config/pipeline/bms-arabic-pages.conf`
- Search Service: `app/Services/UltraFastSearchService.php`

---

## 🔧 المرحلة 1: تطبيق Enhanced Template

### الخطوة 1.1: تطبيق Template على Elasticsearch

```powershell
# قراءة ملف Template
$template = Get-Content "C:\Users\osaid\Documents\BMS_alkamelah\BMS_v1-homev2\logstash-setup\elasticsearch\pages_enhanced_template.json" -Raw -Encoding UTF8

# تطبيق Template
Invoke-RestMethod -Method Put `
    -Uri "http://145.223.98.97:9201/_index_template/pages_enhanced" `
    -ContentType "application/json; charset=utf-8" `
    -Body $template
```

**النتيجة المتوقعة:**

```json
{"acknowledged": true}
```

### الخطوة 1.2: التحقق من تطبيق Template

```powershell
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/_index_template/pages_enhanced"
```

---

## 🔧 المرحلة 2: إنشاء Index جديد

### الخطوة 2.1: إنشاء pages_v3

```powershell
Invoke-RestMethod -Method Put `
    -Uri "http://145.223.98.97:9201/pages_v3" `
    -ContentType "application/json"
```

### الخطوة 2.2: التحقق من الـ Index

```powershell
# التحقق من الإعدادات
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/pages_v3/_settings"

# التحقق من الـ Mapping
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/pages_v3/_mapping"
```

**تأكد من وجود:**

- `arabic_char_filter` في الـ settings
- حقول `content.ngram`, `content.autocomplete`, `content.exact`
- حقل `author_ids` من نوع `keyword`

---

## 🔧 المرحلة 3: تحديث Logstash Pipeline

### الخطوة 3.1: تعديل الملف

**الملف:** `BMS_v1-homev2/logstash-setup/config/pipeline/bms-arabic-pages.conf`

**التغييرات المطلوبة:**

#### 3.1.1: تحديث SQL Query لإضافة author_ids

استبدال الـ query الحالي (سطر 14-35) بهذا:

```sql
SELECT 
  p.id,
  p.page_number,
  p.content,
  p.created_at,
  p.updated_at,
  p.book_id,
  b.title as book_title,
  b.book_section_id,
  a.full_name as book_author,
  bs.name as book_section,
  CHAR_LENGTH(p.content) as content_length,
  GROUP_CONCAT(DISTINCT ab2.author_id) as author_ids_raw
FROM pages p 
LEFT JOIN books b ON p.book_id = b.id 
LEFT JOIN author_book ab ON b.id = ab.book_id AND ab.is_main = 1
LEFT JOIN authors a ON ab.author_id = a.id
LEFT JOIN book_sections bs ON b.book_section_id = bs.id
LEFT JOIN author_book ab2 ON b.id = ab2.book_id
WHERE p.id > :sql_last_value 
GROUP BY p.id, p.page_number, p.content, p.created_at, p.updated_at, 
         p.book_id, b.title, b.book_section_id, a.full_name, bs.name
ORDER BY p.id ASC 
LIMIT 10000
```

#### 3.1.2: إضافة معالجة author_ids في filter section

أضف هذا بعد سطر 191:

```ruby
# تحويل author_ids من string إلى array
if [author_ids_raw] {
  ruby {
    code => "
      raw = event.get('author_ids_raw')
      if raw
        ids = raw.to_s.split(',').map(&:strip).reject(&:empty?)
        event.set('author_ids', ids)
      end
    "
  }
}
mutate {
  remove_field => ["author_ids_raw"]
}
```

#### 3.1.3: تغيير output index

في سطر 217، غيّر:

```ruby
index => "pages"
```

إلى:

```ruby
index => "pages_v3"
```

---

## 🔧 المرحلة 4: إعادة الفهرسة

### الخطوة 4.1: إعادة تعيين last_run

على السيرفر (SSH إلى 145.223.98.97):

```bash
# حذف ملف آخر تشغيل
sudo rm -f /usr/share/logstash/data/.logstash_jdbc_last_run_pages

# أو إنشاء ملف جديد بقيمة 0
echo "--- 0" | sudo tee /usr/share/logstash/data/.logstash_jdbc_last_run_pages
```

### الخطوة 4.2: تشغيل Logstash

```bash
# تشغيل Logstash مع Pipeline الجديد
sudo systemctl restart logstash

# أو تشغيل يدوي للمراقبة
sudo /usr/share/logstash/bin/logstash -f /path/to/bms-arabic-pages.conf
```

### الخطوة 4.3: مراقبة التقدم

```powershell
# عدد الوثائق في Index الجديد
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/pages_v3/_count"

# مقارنة مع القديم (يجب أن يصل إلى ~5 مليون)
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/pages/_count"
```

**الوقت المتوقع:** 3-5 ساعات لـ 5 مليون صفحة

---

## 🔧 المرحلة 5: تحديث Alias

### الخطوة 5.1: إنشاء/تحديث Alias

**انتظر حتى تكتمل الفهرسة!**

```powershell
$aliasBody = @"
{
  "actions": [
    { "add": { "index": "pages_v3", "alias": "pages_active" } }
  ]
}
"@

Invoke-RestMethod -Method Post `
    -Uri "http://145.223.98.97:9201/_aliases" `
    -ContentType "application/json" `
    -Body $aliasBody
```

### الخطوة 5.2: التحقق من Alias

```powershell
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/_alias/pages_active"
```

---

## 🔧 المرحلة 6: تحديث Laravel Code

### الخطوة 6.1: تحديث UltraFastSearchService.php

**الملف:** `app/Services/UltraFastSearchService.php`

#### 6.1.1: تغيير Index Name (سطر 59)

```php
// Before:
$indexToUse = 'pages';

// After:
$indexToUse = 'pages_active'; // Use alias for zero-downtime switching
```

#### 6.1.2: إصلاح فلتر المؤلفين (سطر 616-618)

استبدل:

```php
if (!empty($filters['author_id'])) {
    \Illuminate\Support\Facades\Log::warning('Author filter requested but author_ids field does not exist in Elasticsearch index. Skipping author filter.');
    // TODO: Re-index with author_ids field OR use post-filter with database join
}
```

بـ:

```php
if (!empty($filters['author_id'])) {
    $authorIds = is_array($filters['author_id'])
        ? $filters['author_id']
        : [$filters['author_id']];
    
    // author_ids is keyword type (array of strings)
    $boolQuery['bool']['filter'][] = [
        'terms' => ['author_ids' => array_map('strval', $authorIds)]
    ];
}
```

#### 6.1.3: تحسين البحث الصرفي

الـ Template الحالي لا يحتوي على `content.stemmed`. يستخدم `content` الرئيسي الـ `arabic_enhanced` analyzer الذي يحتوي على stemmer بالفعل.

#### 6.1.4: إضافة حقول فرعية للبحث المرن

في `buildFlexibleMatchQuery` (سطر 271-282)، عدّل الـ `fields`:

```php
return [
    'simple_query_string' => [
        'query' => $searchTerm,
        'fields' => ['content', 'content.ngram^0.5'],  // إضافة ngram للبحث الجزئي
        'default_operator' => $operator,
        'analyze_wildcard' => false,
        'fuzzy_transpositions' => true,
        'fuzzy_max_expansions' => 50,
        'fuzzy_prefix_length' => 1
    ]
];
```

---

## ✅ المرحلة 7: التحقق والاختبار

### الخطوة 7.1: اختبار Character Normalization

```powershell
# اختبار بحث "صلاة" (بالتاء المربوطة)
$body = @"
{
  "query": {
    "match": {
      "content": "صلاة"
    }
  },
  "size": 3
}
"@

Invoke-RestMethod -Method Post `
    -Uri "http://145.223.98.97:9201/pages_v3/_search" `
    -ContentType "application/json; charset=utf-8" `
    -Body $body
```

**يجب أن يجد نتائج تحتوي على "صلاه" أيضاً!**

### الخطوة 7.2: اختبار author_ids

```powershell
# التحقق من وجود author_ids في الوثائق
$body = @"
{
  "query": {
    "exists": {
      "field": "author_ids"
    }
  },
  "size": 5,
  "_source": ["book_id", "book_title", "author_ids"]
}
"@

Invoke-RestMethod -Method Post `
    -Uri "http://145.223.98.97:9201/pages_v3/_search" `
    -ContentType "application/json" `
    -Body $body
```

### الخطوة 7.3: اختبار على الموقع

1. افتح صفحة البحث على الموقع
2. ابحث عن: `صلاة` (بالتاء المربوطة)
3. تأكد من ظهور نتائج
4. جرّب فلتر المؤلفين

---

## ⚠️ توصيات هامة

### 1. النسخ الاحتياطي

```powershell
# قبل أي تغيير، تأكد من وجود الـ index القديم
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/_cat/indices?v"
```

### 2. Zero-Downtime

- استخدم `pages_active` alias بدلاً من `pages_v3` مباشرة
- هذا يسمح بالتبديل الفوري بدون توقف

### 3. المراقبة

```powershell
# مراقبة صحة الـ Cluster
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/_cluster/health"

# مراقبة استخدام الذاكرة
Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/_nodes/stats/jvm"
```

### 4. Rollback Plan

إذا حدثت مشكلة:

```powershell
# ارجع الـ alias للـ index القديم
$rollback = @"
{
  "actions": [
    { "remove": { "index": "pages_v3", "alias": "pages_active" } },
    { "add": { "index": "pages", "alias": "pages_active" } }
  ]
}
"@

Invoke-RestMethod -Method Post `
    -Uri "http://145.223.98.97:9201/_aliases" `
    -ContentType "application/json" `
    -Body $rollback
```

---

## 📊 ملخص الوقت المتوقع

| المرحلة | الوقت |
|---------|-------|
| تطبيق Template | 2 دقيقة |
| إنشاء Index | 1 دقيقة |
| تحديث Logstash | 15 دقيقة |
| إعادة الفهرسة | 3-5 ساعات |
| تحديث Alias | 2 دقيقة |
| تحديث Laravel | 30 دقيقة |
| الاختبار | 30 دقيقة |
| **الإجمالي** | **~5-6 ساعات** |

---

## 📈 النتائج المتوقعة بعد التطبيق

| الكلمة | قبل | بعد |
|--------|-----|-----|
| صلاة | 0 ❌ | 10,000+ ✅ |
| قرآن | 0 ❌ | 10,000+ ✅ |
| إسلام | 0 ❌ | 10,000+ ✅ |
| أحمد | 0 ❌ | 10,000+ ✅ |

**التحسينات:**

- ✅ زيادة دقة البحث: **60-80%**
- ✅ جميع الفلاتر تعمل (مطابق، مرن، صرفي، ضبابي، بداية)
- ✅ فلتر المؤلفين يعمل
- ✅ البحث بأي شكل من الحرف (ة/ه، آ/ا، إ/أ/ا)
