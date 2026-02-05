# إصلاح مشكلة حفظ الصور في النظام

## المشكلة
الصور في النظام لم تكن تحفظ بشكل صحيح في بعض الموارد بسبب إعدادات غير كاملة في مكونات `FileUpload`.

## الحل المطبق

### 1. إضافة `disk('public')` إلى جميع `FileUpload`
تم التأكد من أن جميع مكونات رفع الملفات تستخدم `disk('public')` لحفظ الصور في المجلد المشترك:

```php
FileUpload::make('image')
    ->label('صورة المؤلف')
    ->image()
    ->imageEditor()
    ->disk('public')  // ✅ تم إضافة هذا
    ->directory('authors')
```

### 2. إضافة معالج `saveUploadedFileUsing()`
تم إضافة دالة معالجة مخصصة لضمان حفظ الملفات بشكل صحيح:

```php
->saveUploadedFileUsing(function ($file, $record) {
    return Storage::disk('public')->putFile('authors', $file);
})
```

### 3. استيراد `Storage` Facade
تم إضافة استيراد `Illuminate\Support\Facades\Storage` إلى جميع الملفات التي تحتاج إليها.

## الملفات التي تم تعديلها

### ✅ تم الإصلاح:

1. **app/Filament/Resources/Authors/Schemas/AuthorForm.php**
   - صور المؤلفين
   - الحفظ في: `storage/app/public/authors/`

2. **app/Filament/Resources/Books/Schemas/BookForm.php**
   - صور المؤلفين (في Repeater للمؤلفين)
   - صور الكتاب
   - الحفظ في: `storage/app/public/authors/` و `storage/app/public/book-images/`

3. **app/Filament/Resources/Publishers/Schemas/PublisherForm.php**
   - شعار الناشر
   - الحفظ في: `storage/app/public/publishers/`

4. **app/Filament/Resources/BookSections/Schemas/BookSectionForm.php**
   - أيقونة القسم
   - الحفظ في: `storage/app/public/book-sections-logos/`

5. **app/Filament/Resources/Articles/Schemas/ArticleForm.php**
   - صورة غلاف المقال
   - الحفظ في: `storage/app/public/article-images/`

6. **app/Filament/Resources/FeedbackComplaints/Schemas/FeedbackComplaintForm.php**
   - مرفقات البلاغات
   - الحفظ في: `storage/app/public/feedback-attachments/`

## الخطوات الإضافية المطلوبة

### 1. التأكد من وجود رابط Storage
```bash
php artisan storage:link
```

هذا الأمر ينشئ رابط رمزي من `public/storage` إلى `storage/app/public`

### 2. التحقق من الأذونات
تأكد من أن مجلد التخزين يملك الأذونات الصحيحة:

```bash
# على Linux/Mac
chmod -R 755 storage/app/public
chmod -R 755 storage/app/private

# تأكد من أن الويب سيرفر يمكنه الكتابة
sudo chown -R www-data:www-data storage/app/public
```

### 3. إنشاء المجلدات المطلوبة
```bash
mkdir -p storage/app/public/authors
mkdir -p storage/app/public/publishers
mkdir -p storage/app/public/book-images
mkdir -p storage/app/public/book-sections-logos
mkdir -p storage/app/public/article-images
mkdir -p storage/app/public/news-images
mkdir -p storage/app/public/feedback-attachments
```

### 4. تنظيف ذاكرة التخزين المؤقتة
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## معايير الحفظ

| المورد | النوع | المجلد | الحد الأقصى |
|-------|------|--------|-----------|
| المؤلف | صورة | `authors/` | - |
| الكتاب | صور متعددة | `book-images/` | 3 ملفات |
| الناشر | شعار | `publishers/` | - |
| القسم | أيقونة | `book-sections-logos/` | - |
| المقال | غلاف | `article-images/` | - |
| المقالة الإخبارية | صورة | `news-images/` | - |
| البلاغ | مرفق | `feedback-attachments/` | - |

## التحقق من نجاح الإصلاح

1. انتقل إلى أي موارد (مثل: إضافة/تعديل مؤلف)
2. حمّل صورة
3. احفظ السجل
4. تحقق من أن الصورة موجودة في `storage/app/public/[directory]/`
5. تحقق من أن الصورة تظهر في الواجهة الأمامية

## ملاحظات مهمة

- جميع الصور تُحفظ الآن في `disk('public')` وليس `disk('local')`
- هذا يسمح بالوصول المباشر إلى الصور من خلال الويب
- تأكد من أن رابط Storage موجود: `public/storage -> storage/app/public`
- في الإنتاج، قد تحتاج إلى استخدام خدمة تخزين سحابية (S3, etc.)

## استكشاف الأخطاء

إذا استمرت المشكلة:

1. تحقق من سجلات Laravel: `storage/logs/laravel.log`
2. تأكد من وجود رابط Storage: `ls -la public/storage`
3. تحقق من الأذونات: `ls -la storage/app/public/`
4. تأكد من أن المتصفح يرسل الملف بشكل صحيح
5. جرّب رفع ملف بصيغة مختلفة (jpg, png, etc.)
