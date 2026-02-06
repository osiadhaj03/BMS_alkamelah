# مراقبة تقدم الفهرسة

Write-Host "=== مراقبة الفهرسة ===" -ForegroundColor Cyan
Write-Host "سيتم التحقق كل دقيقة... اضغط Ctrl+C للإيقاف`n" -ForegroundColor Yellow

$startTime = Get-Date
$targetCount = 5041518  # عدد الصفحات المتوقع

while ($true) {
    try {
        # الحصول على عدد الوثائق
        $result = Invoke-RestMethod -Method Get -Uri "http://145.223.98.97:9201/pages_v3/_count" -ErrorAction Stop
        $currentCount = $result.count
        
        # حساب النسبة والوقت المتبقي
        $percentage = [math]::Round(($currentCount / $targetCount) * 100, 2)
        $elapsed = (Get-Date) - $startTime
        
        if ($currentCount -gt 0) {
            $rate = $currentCount / $elapsed.TotalSeconds
            $remaining = ($targetCount - $currentCount) / $rate
            $eta = [TimeSpan]::FromSeconds($remaining)
        } else {
            $eta = "غير معروف"
        }
        
        # عرض التقدم
        $timestamp = Get-Date -Format "HH:mm:ss"
        Write-Host "[$timestamp] " -NoNewline -ForegroundColor Gray
        Write-Host "الوثائق: " -NoNewline
        Write-Host "$currentCount" -NoNewline -ForegroundColor Green
        Write-Host " / $targetCount " -NoNewline
        Write-Host "($percentage%)" -NoNewline -ForegroundColor Cyan
        
        if ($currentCount -gt 0) {
            Write-Host " | الوقت المتبقي: " -NoNewline
            Write-Host "$($eta.Hours)h $($eta.Minutes)m" -ForegroundColor Yellow
        } else {
            Write-Host " | في انتظار البدء..." -ForegroundColor Yellow
        }
        
        # التحقق من الاكتمال
        if ($currentCount -ge $targetCount) {
            Write-Host "`n✅ اكتملت الفهرسة!" -ForegroundColor Green
            
            # اختبار سريع
            Write-Host "`nاختبار البحث..." -ForegroundColor Cyan
            $testBody = @"
{
  "query": {
    "match": {
      "content": "صلاة"
    }
  },
  "size": 1
}
"@
            $testResult = Invoke-RestMethod -Method Post -Uri "http://145.223.98.97:9201/pages_v3/_search" -ContentType "application/json; charset=utf-8" -Body $testBody
            
            $hits = $testResult.hits.total.value
            Write-Host "✅ البحث عن صلاة وجد $hits نتائج" -ForegroundColor Green
            break
        }
        
    } catch {
        $timestamp = Get-Date -Format "HH:mm:ss"
        Write-Host "[$timestamp] ❌ خطأ في الاتصال: $($_.Exception.Message)" -ForegroundColor Red
    }
    
    Start-Sleep -Seconds 60
}

Write-Host "`nيمكنك الآن استخدام الموقع!" -ForegroundColor Green
