# سكريبت رفع وتشغيل Logstash

$server = "145.223.98.97"
$user = "root"
$password = "Zr9.(8HDo+U1X5nJ&6eY"
$localFile = "C:\Users\osaid\Documents\BMS_alkamelah\BMS_v1-homev2\logstash-setup\config\pipeline\bms-arabic-pages.conf"

Write-Host "=== بدء إعداد الفهرسة ===" -ForegroundColor Cyan

# 1. رفع الملف باستخدام PSFTP أو WinSCP
Write-Host "`n1. رفع ملف Logstash..." -ForegroundColor Yellow

# استخدام plink لتنفيذ الأوامر
$plinkPath = "plink.exe"
$pscpPath = "pscp.exe"

# التحقق من وجود PuTTY
if (!(Get-Command $pscpPath -ErrorAction SilentlyContinue)) {
    Write-Host "❌ PSCP غير مثبت. استخدم WinSCP يدوياً:" -ForegroundColor Red
    Write-Host "الملف المحلي: $localFile" -ForegroundColor White
    Write-Host "الوجهة: root@$server:/etc/logstash/conf.d/bms-arabic-pages.conf" -ForegroundColor White
    Write-Host "`nأو نفذ هذه الأوامر على السيرفر مباشرة:" -ForegroundColor Yellow
    Write-Host @"
ssh root@$server
# انسخ محتوى الملف المحدث إلى:
nano /etc/logstash/conf.d/bms-arabic-pages.conf
# ثم نفذ:
rm -f /usr/share/logstash/data/.logstash_jdbc_last_run_pages
systemctl restart logstash
journalctl -u logstash -f
"@ -ForegroundColor White
    
    pause
    exit
}

# رفع الملف
Write-Host "رفع الملف..." -ForegroundColor Cyan
echo $password | & $pscpPath -pw $password $localFile "${user}@${server}:/etc/logstash/conf.d/bms-arabic-pages.conf"

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ تم رفع الملف بنجاح" -ForegroundColor Green
} else {
    Write-Host "❌ فشل رفع الملف" -ForegroundColor Red
    exit
}

# 2. تنفيذ الأوامر على السيرفر
Write-Host "`n2. إعادة تعيين last_run..." -ForegroundColor Yellow
echo $password | & $plinkPath -pw $password "${user}@${server}" "rm -f /usr/share/logstash/data/.logstash_jdbc_last_run_pages && echo 'Deleted last_run file'"

Write-Host "`n3. إعادة تشغيل Logstash..." -ForegroundColor Yellow
echo $password | & $plinkPath -pw $password "${user}@${server}" "systemctl restart logstash && echo 'Logstash restarted'"

Write-Host "`n✅ تم بدء الفهرسة!" -ForegroundColor Green
Write-Host "`nاستخدم monitor-reindexing.ps1 لمراقبة التقدم" -ForegroundColor Cyan
