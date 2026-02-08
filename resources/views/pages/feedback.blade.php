@extends('layouts.app')

@section('title', 'الشكاوي والملاحظات')

@section('content')
    <!-- Header -->
    @include('components.layout.header')

    <div class="min-h-screen bg-[#fafafa] relative overflow-hidden">
        <!-- Section Background Pattern -->
        <div class="absolute inset-0 pointer-events-none"
            style="background-image: url('{{ asset('assets/Frame 1321314420.png') }}'); background-repeat: repeat; background-size: 800px; background-attachment: fixed;">
        </div>

        <section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20" dir="rtl">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center">
                        <svg class="w-full h-full text-[#1a3a2a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1a3a2a]">الشكاوي والملاحظات</h2>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-[2rem] shadow-xl shadow-green-900/5 overflow-hidden border border-gray-100 p-8 md:p-12"
                x-data="feedbackForm()">

                <!-- Title -->
                <h3 class="text-2xl md:text-3xl font-bold text-[#2C6E4A] mb-4 text-center">
                    نسعد بتلقي ملاحظاتكم واقتراحاتكم
                </h3>
                <p class="text-gray-600 text-center mb-8">
                    رأيكم يهمنا! شاركنا بشكواك أو ملاحظتك لتحسين خدماتنا
                </p>

                <!-- Error Messages -->
                <div x-show="errors.length > 0" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-red-600 text-sm">
                        <template x-for="error in errors" :key="error">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                </div>

                <!-- Success Message -->
                <div x-show="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-600 font-semibold" x-text="'✓ ' + successMessage"></p>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Type and Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">نوع البلاغ *</label>
                            <select x-model="form.type"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                                :disabled="loading" required>
                                <option value="">اختر النوع</option>
                                <option value="complaint">شكوى</option>
                                <option value="suggestion">اقتراح</option>
                                <option value="feedback">ملاحظة</option>
                                <option value="inquiry">استفسار</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">التصنيف *</label>
                            <select x-model="form.category"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                                :disabled="loading" required>
                                <option value="">اختر التصنيف</option>
                                <option value="book">كتاب معين</option>
                                <option value="author">مؤلف معين</option>
                                <option value="search">نتائج البحث</option>
                                <option value="page">صفحة في الموقع</option>
                                <option value="general">عام</option>
                            </select>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">الموضوع *</label>
                        <input type="text" x-model="form.subject" placeholder="عنوان مختصر للبلاغ"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                            :disabled="loading" required>
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">الرسالة *</label>
                        <textarea x-model="form.message" placeholder="اكتب رسالتك بالتفصيل..." rows="6"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right resize-none"
                            :disabled="loading" required></textarea>
                    </div>

                    <!-- Personal Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">الاسم *</label>
                            <input type="text" x-model="form.name" placeholder="اسمك الكامل"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                                :disabled="loading" required>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">البريد الإلكتروني *</label>
                            <input type="email" x-model="form.email" placeholder="example@email.com"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                                :disabled="loading" required>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">رقم الهاتف (اختياري)</label>
                            <input type="tel" x-model="form.phone" placeholder="05xxxxxxxx"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                                :disabled="loading">
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">إرفاق ملف (اختياري)</label>
                        <input type="file" @change="handleFileUpload"
                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all"
                            :disabled="loading">
                        <p class="text-sm text-gray-500 mt-2">* الملفات المدعومة: JPG, PNG, PDF, DOC, DOCX (حد أقصى 5MB)</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit"
                            class="px-12 py-4 bg-[#2C6E4A] text-white rounded-full font-bold text-lg hover:bg-[#245a3d] transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                            :disabled="loading">
                            <span x-show="!loading">إرسال البلاغ</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                جاري الإرسال...
                            </span>
                        </button>
                    </div>
                </form>

                <script>
                    function feedbackForm() {
                        return {
                            form: {
                                type: '',
                                category: '',
                                subject: '',
                                message: '',
                                name: '',
                                email: '',
                                phone: '',
                                attachment: null,
                            },
                            
                            init() {
                                const params = new URLSearchParams(window.location.search);
                                if (params.has('type')) this.form.type = params.get('type');
                                if (params.has('category')) this.form.category = params.get('category');
                                if (params.has('subject')) this.form.subject = params.get('subject');
                                if (params.has('message')) this.form.message = params.get('message');
                            },
                            errors: [],
                            successMessage: '',
                            loading: false,

                            handleFileUpload(event) {
                                this.form.attachment = event.target.files[0];
                            },

                            async submitForm() {
                                this.errors = [];
                                this.successMessage = '';
                                this.loading = true;

                                try {
                                    const formData = new FormData();
                                    for (const key in this.form) {
                                        if (this.form[key] !== null && this.form[key] !== '') {
                                            formData.append(key, this.form[key]);
                                        }
                                    }

                                    const response = await fetch('{{ route('feedback.store') }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        },
                                        body: formData
                                    });

                                    const data = await response.json();

                                    if (!response.ok) {
                                        if (data.errors) {
                                            this.errors = Object.values(data.errors).flat();
                                        } else {
                                            this.errors = [data.message || 'حدث خطأ ما'];
                                        }
                                    } else {
                                        this.successMessage = data.message;
                                        this.form = {
                                            type: '',
                                            category: '',
                                            subject: '',
                                            message: '',
                                            name: '',
                                            email: '',
                                            phone: '',
                                            attachment: null,
                                        };

                                        // Reset file input
                                        document.querySelector('input[type="file"]').value = '';

                                        // Hide success message after 5 seconds
                                        setTimeout(() => {
                                            this.successMessage = '';
                                        }, 5000);

                                        // Scroll to top
                                        window.scrollTo({ top: 0, behavior: 'smooth' });
                                    }
                                } catch (error) {
                                    this.errors = ['حدث خطأ في الاتصال'];
                                    console.error('Error:', error);
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }
                    }
                </script>
            </div>
        </section>
    </div>

    <!-- Footer -->
    @include('components.layout.footer')
@endsection
