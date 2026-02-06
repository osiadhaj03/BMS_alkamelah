# الطريقة اليدوية السريعة - انسخ والصق في SSH

# 1. اتصل بالسيرفر
ssh root@145.223.98.97
# Password: Zr9.(8HDo+U1X5nJ&6eY

# 2. حذف last_run للبدء من الصفر
rm -f /usr/share/logstash/data/.logstash_jdbc_last_run_pages

# 3. نسخ الملف المحدث (خيار 1: استخدام nano)
nano /etc/logstash/conf.d/bms-arabic-pages.conf
# اضغط Ctrl+K عدة مرات لحذف المحتوى القديم
# انسخ محتوى الملف من:
# C:\Users\osaid\Documents\BMS_alkamelah\BMS_v1-homev2\logstash-setup\config\pipeline\bms-arabic-pages.conf
# الصقه في nano
# اضغط Ctrl+O ثم Enter للحفظ
# اضغط Ctrl+X للخروج

# 4. إعادة تشغيل Logstash
systemctl restart logstash

# 5. مراقبة السجلات
journalctl -u logstash -f

# لإيقاف المراقبة: اضغط Ctrl+C
# للخروج من SSH: اكتب exit
