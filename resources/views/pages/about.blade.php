@extends('layouts.app')

@section('title', 'من نحن')

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
                        <img src="{{ asset('images/group0.svg') }}" alt="Icon" class="w-full h-full object-contain">
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1a3a2a]">من نحن</h2>
                </div>
            </div>

            <!-- Content Card -->
            <div
                class="bg-white rounded-[2rem] shadow-xl shadow-green-900/5 overflow-hidden border border-gray-100 p-8 md:p-12">

                <!-- Title -->
                <h3 class="text-2xl md:text-3xl font-bold text-[#2C6E4A] mb-8 text-center">
                    وقف الأقصى الشريف للمعرفة
                </h3>

                <!-- Main Text -->
                <div class="prose prose-lg max-w-none text-gray-700 leading-loose"
                    style="font-size: 1.15rem; line-height: 2.2;">
                    <p class="mb-6 text-justify">
                        يسرنا المساهمة في مشروع الكتاب البحثي في زمن تطور المعرفة وانتشارها وشيوع كتاب PDF بعد كثرة الطباعة
                        الورقية.
                    </p>

                    <p class="mb-6 text-justify">
                        ظهرت الحاجة الكبيرة للكتاب البحثي، وهو لا يغني عن الكتاب الورقي أو الـ PDF، وإنما هي صيغة أخرى في
                        نشر العلم والوصول لكل النتائج.
                    </p>

                    <p class="mb-6 text-justify">
                        لذلك ينبغي للكل من مؤلفين ومحققين ودور نشر تسعى لنشر العلم بالطريقة البحثية أن يساعدوا بإرسال
                        مؤلفاتهم وتحقيقاتهم على موقعنا لتكون من الكتب الوقفية لله لينتفع بها الناس.
                    </p>

                    <p class="text-justify font-semibold text-[#2C6E4A]">
                        وهي لا شك أسرع طريق للاستفادة من الكتاب وشيوعه.
                    </p>
                </div>

                <!-- Decorative Element -->
                <div class="mt-12 flex justify-center">
                    <div class="w-24 h-1 bg-gradient-to-r from-[#2C6E4A] to-[#4A9B6D] rounded-full"></div>
                </div>

                <!-- Contact Section -->
                <div class="mt-12 text-center">
                    <h4 class="text-xl font-bold text-[#1a3a2a] mb-4">تواصل معنا</h4>
                    <p class="text-gray-600 mb-6">للمساهمة في المشروع أو إرسال مؤلفاتكم</p>
                    <div class="flex justify-center gap-4 flex-wrap">
                        <a href="mailto:info@example.com"
                            class="inline-flex items-center gap-2 px-8 py-3 bg-[#2C6E4A] text-white rounded-full font-bold hover:bg-[#245a3d] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            راسلنا
                        </a>
                        <a href="#"
                            class="inline-flex items-center gap-2 px-8 py-3 bg-white text-[#2C6E4A] border-2 border-[#2C6E4A] rounded-full font-bold hover:bg-[#2C6E4A] hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                            إرسال كتاب
                        </a>
                    </div>
                </div>

                <!-- Decorative Element -->
                <div class="mt-12 flex justify-center">
                    <div class="w-24 h-1 bg-gradient-to-r from-[#2C6E4A] to-[#4A9B6D] rounded-full"></div>
                </div>

                <!-- Social Media Section -->
                <div class="mt-12 text-center">
                    <h4 class="text-xl font-bold text-[#1a3a2a] mb-6">تابعونا على مواقع التواصل الاجتماعي</h4>
                    <div class="flex justify-center gap-5 flex-wrap">
                        <!-- WhatsApp -->
                        <a href="#"
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-[#e8f5e9] text-[#2C6E4A] hover:bg-[#2C6E4A] hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                        </a>
                        <!-- Telegram -->
                        <a href="#"
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-[#e8f5e9] text-[#2C6E4A] hover:bg-[#2C6E4A] hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
                            </svg>
                        </a>
                        <!-- Facebook -->
                        <a href="#"
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-[#e8f5e9] text-[#2C6E4A] hover:bg-[#2C6E4A] hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <!-- Instagram -->
                        <a href="#"
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-[#e8f5e9] text-[#2C6E4A] hover:bg-[#2C6E4A] hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                            </svg>
                        </a>
                        <!-- YouTube -->
                        <a href="#"
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-[#e8f5e9] text-[#2C6E4A] hover:bg-[#2C6E4A] hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                        <!-- X (Twitter) -->
                        <a href="#"
                            class="w-12 h-12 flex items-center justify-center rounded-full bg-[#e8f5e9] text-[#2C6E4A] hover:bg-[#2C6E4A] hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Decorative Element -->
                <div class="mt-12 flex justify-center">
                    <div class="w-24 h-1 bg-gradient-to-r from-[#2C6E4A] to-[#4A9B6D] rounded-full"></div>
                </div>

                <!-- Newsletter Section -->
                <div class="mt-12">
                    <div class="bg-gradient-to-r from-[#e8f5e9] to-[#f0fdf4] rounded-2xl p-8 text-center">
                        <h4 class="text-xl font-bold text-[#1a3a2a] mb-2">اشترك في النشرة البريدية</h4>
                        <p class="text-gray-600 mb-6">ليصلك كل جديد من الكتب والإصدارات</p>

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <ul class="text-red-600 text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-green-600 font-semibold">✓ {{ session('success') }}</p>
                            </div>
                        @endif

                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="max-w-xl mx-auto space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="name" placeholder="الاسم الكامل"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                                    value="{{ old('name') }}"
                                    required>
                                <input type="email" name="email" placeholder="البريد الإلكتروني"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                                    value="{{ old('email') }}"
                                    required>
                            </div>
                            <div class="flex items-center gap-4">
                                <input type="tel" name="phone" placeholder="رقم الهاتف (اختياري - للإشعارات عبر واتساب)"
                                    class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:border-[#2C6E4A] focus:ring-2 focus:ring-[#2C6E4A]/20 outline-none transition-all text-right"
                                    value="{{ old('phone') }}">
                            </div>
                            <p class="text-sm text-gray-500">* أدخل رقم الهاتف لتصلك إشعارات الكتب الجديدة عبر واتساب</p>
                            <button type="submit"
                                class="w-full md:w-auto px-8 py-3 bg-[#2C6E4A] text-white rounded-lg font-bold hover:bg-[#245a3d] transition-colors">
                                اشترك الآن
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    @include('components.layout.footer')
@endsection