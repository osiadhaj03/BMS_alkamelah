<footer class="bg-green-800 text-white py-12" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Logo and Description -->
            <div>
                <h3 class="text-2xl font-bold mb-4">مكتبة الكاملة</h3>
                <p class="text-green-100">
                    مكتبة شاملة تحتوي على آلاف الكتب في مختلف العلوم الإسلامية والعربية
                </p>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4">روابط سريعة</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-green-100 hover:text-white">الرئيسية</a></li>
                    <li><a href="#" class="text-green-100 hover:text-white">الكتب</a></li>
                    <li><a href="#" class="text-green-100 hover:text-white">المؤلفين</a></li>
                    <li><a href="#" class="text-green-100 hover:text-white">الأقسام</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-semibold mb-4">تواصل معنا</h4>
                <div class="space-y-2 text-green-100">
                    <p>البريد الإلكتروني: info@alkamelah.com</p>
                    <p>الموقع: www.alkamelah.com</p>
                </div>
            </div>
        </div>
        
        <div class="border-t border-green-700 mt-8 pt-8 text-center">
            <p class="text-green-100">
                &copy; {{ date('Y') }} مكتبة الكاملة. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</footer>