# سكريبت بدء الفهرسة

Write-Host "=== البدء في الفهرسة ===" -ForegroundColor Cyan

# 1. رفع ملف Logstash المحدث إلى السيرفر
Write-Host "`n1. رفع ملف Logstash..." -ForegroundColor Yellow
$localFile = "C:\Users\osaid\Documents\BMS_alkamelah\BMS_v1-homev2\logstash-setup\config\pipeline\bms-arabic-pages.conf"
$remoteFile = "root@145.223.98.97:/etc/logstash/conf.d/bms-arabic-pages.conf"

# استخدم SCP لرفع الملف
try {
    scp $localFile $remoteFile
    Write-Host "✅ تم رفع الملف بنجاح" -ForegroundColor Green
} catch {
    Write-Host "❌ فشل رفع الملف. تأكد من تثبيت OpenSSH" -ForegroundColor Red
    Write-Host "يمكنك رفع الملف يدوياً عبر SFTP" -ForegroundColor Yellow
    pause
}

# 2. إعادة تعيين last_run
Write-Host "`n2. إعادة تعيين last_run..." -ForegroundColor Yellow
Write-Host "يجب تنفيذ هذا الأمر على السيرفر عبر SSH:" -ForegroundColor Cyan
Write-Host "sudo rm -f /usr/share/logstash/data/.logstash_jdbc_last_run_pages" -ForegroundColor White

# 3. إعادة تشغيل Logstash
Write-Host "`n3. إعادة تشغيل Logstash..." -ForegroundColor Yellow
Write-Host "يجب تنفيذ هذا الأمر على السيرفر عبر SSH:" -ForegroundColor Cyan
Write-Host "sudo systemctl restart logstash" -ForegroundColor White

Write-Host "`n=== الخطوات التالية ===" -ForegroundColor Cyan
Write-Host "1. اتصل بالسيرفر: ssh root@145.223.98.97" -ForegroundColor Yellow
Write-Host "2. نفذ: sudo rm -f /usr/share/logstash/data/.logstash_jdbc_last_run_pages" -ForegroundColor Yellow
Write-Host "3. نفذ: sudo systemctl restart logstash" -ForegroundColor Yellow
Write-Host "4. راقب السجلات: sudo journalctl -u logstash -f" -ForegroundColor Yellow
Write-Host "`nأو استخدم monitor-reindexing.ps1 لمراقبة التقدم من هنا" -ForegroundColor Green
