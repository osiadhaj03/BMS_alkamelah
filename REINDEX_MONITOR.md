# 🔄 مراقبة عملية إعادة الفهرسة

## معلومات العملية

- **Index الجديد:** `pages_v2_20260205_222346`
- **Task ID:** `MmIVKLtCRQ2RhYoyVkAP6A:5394732`
- **وقت البدء:** 2026-02-05 22:23:46
- **المصدر:** pages (5,041,518 documents)
- **الهدف:** pages_v2_20260205_222346 (مع Enhanced Template)

## الحالة الحالية

- ✅ **Template applied:** pages_enhanced
- ✅ **Index created:** pages_v2_20260205_222346
- 🔄 **Reindex started:** In progress
- **Progress:** 20,000 / 5,041,518 (0.4%)
- **Rate:** ~371 docs/sec
- **الوقت المتوقع:** ~3.8 ساعات

## كيفية مراقبة التقدم

استخدم هذا الأمر في PowerShell:

```powershell
# قراءة Task ID
$taskId = Get-Content "C:\Users\osaid\Documents\BMS_alkamelah\reindex_task_id.txt" -Raw
$taskId = $taskId.Trim()

# التحقق من التقدم
$response = Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/_tasks/$taskId"

# عرض النتائج
if(-not $response.completed) {
    $status = $response.task.status
    $created = $status.created
    $total = $status.total
    $percent = [math]::Round(($created / $total) * 100, 2)
    
    Write-Host "Progress: $created / $total ($percent%)" -ForegroundColor Cyan
    Write-Host "Updated: $($status.updated)" -ForegroundColor Yellow
} else {
    Write-Host "✅ Reindex completed!" -ForegroundColor Green
}
```

## اختبار Index الجديد (بعد الانتهاء)

```powershell
# قراءة اسم Index الجديد
$newIndex = Get-Content "C:\Users\osaid\Documents\BMS_alkamelah\new_index_name.txt" -Raw
$newIndex = $newIndex.Trim()

# اختبار "صلاة" (يجب أن يجد نتائج الآن!)
$body = '{"query":{"match":{"content":"صلاة"}},"size":5}'
$response = Invoke-RestMethod -Method Post `
    -Uri "http://145.223.98.97:9201/$newIndex/_search" `
    -ContentType "application/json; charset=utf-8" `
    -Body $body

Write-Host "نتائج البحث عن 'صلاة': $($response.hits.total.value)" -ForegroundColor Green

# اختبار "قرآن"
$body2 = '{"query":{"match":{"content":"قرآن"}},"size":5}'
$response2 = Invoke-RestMethod -Method Post `
    -Uri "http://145.223.98.97:9201/$newIndex/_search" `
    -ContentType "application/json; charset=utf-8" `
    -Body $body2

Write-Host "نتائج البحث عن 'قرآن': $($response2.hits.total.value)" -ForegroundColor Green

# اختبار "إسلام"
$body3 = '{"query":{"match":{"content":"إسلام"}},"size":5}'
$response3 = Invoke-RestMethod -Method Post `
    -Uri "http://145.223.98.97:9201/$newIndex/_search" `
    -ContentType "application/json; charset=utf-8" `
    -Body $body3

Write-Host "نتائج البحث عن 'إسلام': $($response3.hits.total.value)" -ForegroundColor Green
```

## الخطوات التالية (بعد انتهاء Reindex)

### 1. التحقق من العدد
```powershell
$newIndex = (Get-Content "C:\Users\osaid\Documents\BMS_alkamelah\new_index_name.txt").Trim()
$response = Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/$newIndex/_count"
Write-Host "Total documents: $($response.count)"
# يجب أن يكون: 5,041,518
```

### 2. إنشاء Alias
```powershell
$aliasBody = @"
{
  "actions": [
    { "add": { "index": "$newIndex", "alias": "pages_search" } }
  ]
}
"@

Invoke-RestMethod -Method Post `
    -Uri "http://145.223.98.97:9201/_aliases" `
    -ContentType "application/json; charset=utf-8" `
    -Body $aliasBody

Write-Host "✅ Alias 'pages_search' created!"
```

### 3. تحديث Laravel
في `app/Services/UltraFastSearchService.php`:

```php
// استبدل 'pages' بـ 'pages_search'
protected string $index = 'pages_search';
```

### 4. حذف Index القديم (اختياري - بعد التأكد)
```powershell
# احذف Index القديم فقط بعد التأكد من نجاح كل شيء
# Invoke-RestMethod -Method Delete -Uri "http://145.223.98.97:9201/pages"
```

## الملاحظات

- ⏰ **الوقت المتوقع:** 3-4 ساعات
- 💾 **المساحة المستخدمة:** ضعف حجم Index الحالي مؤقتاً
- 🔒 **Downtime:** صفر! Index القديم يعمل بشكل طبيعي
- 🎯 **النتيجة المتوقعة:** البحث عن "صلاة" و "قرآن" و "إسلام" سيعمل!

## حساب الوقت المتبقي

بناءً على المعدل الحالي (371 docs/sec):

- **Total:** 5,041,518 documents
- **Rate:** ~371 docs/sec
- **Time:** 5,041,518 / 371 = ~13,590 seconds ≈ **3.8 hours**

سيكون جاهزاً تقريباً في: **02:00 صباحاً** (5 فبراير 2026)
