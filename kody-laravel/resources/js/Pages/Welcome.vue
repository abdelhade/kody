<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    isLoggedIn: Boolean,
    tenantUrl: String
});

const isMobileMenuOpen = ref(false);
</script>

<template>
    <Head title="الصفحة الرئيسية - نظام الحسابات المبسط" />

    <div class="min-h-screen bg-white">
        <!-- Navigation Bar -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="shrink-0 flex items-center">
                            <span class="text-2xl font-bold text-[#714B67]">نظام حسابات مبسط</span>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:mr-10 sm:flex gap-4">
                            <a href="#features" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">المميزات</a>
                            <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">الأسعار</a>
                            <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">تواصل معنا</a>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:mr-6 gap-4">
                        <template v-if="isLoggedIn">
                            <a :href="tenantUrl" class="bg-[#714B67] text-white px-6 py-2 rounded-md text-sm font-bold hover:bg-[#5a3a52] transition duration-150 shadow-md">
                                الدخول إلى نظام شركتك
                            </a>
                            <a href="/logout" class="text-sm text-gray-500 hover:text-red-600 font-medium ml-4 transition">
                                تسجيل خروج
                            </a>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm text-gray-700 hover:text-gray-900 font-medium">تسجيل الدخول</Link>
                            <Link :href="route('register')" class="bg-[#714B67] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#5a3a52] transition duration-150">ابدأ الآن مجاناً</Link>
                        </template>
                    </div>
                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': isMobileMenuOpen, 'inline-flex': !isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !isMobileMenuOpen, 'inline-flex': isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div :class="{'block': isMobileMenuOpen, 'hidden': !isMobileMenuOpen}" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1 text-right pr-4">
                    <a href="#features" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">المميزات</a>
                    <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">الأسعار</a>
                </div>
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="mt-3 space-y-1 text-right pr-4">
                        <template v-if="isLoggedIn">
                            <a :href="tenantUrl" class="block px-4 py-2 text-base font-bold text-[#714B67] hover:bg-gray-100">الدخول إلى نظام شركتك</a>
                            <a href="/logout" class="block px-4 py-2 text-base font-medium text-red-500 hover:text-red-700 hover:bg-gray-100 mt-2">تسجيل خروج</a>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">تسجيل الدخول</Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32 pt-20">
                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28 text-right">
                        <div class="sm:text-center lg:text-right">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block xl:inline">أدوات متكاملة لإدارة</span>
                                <span class="block text-[#017E84] xl:inline mt-2"> حساباتك بسهولة</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                نظام محاسبي مبسط يجمع لك المبيعات، المشتريات، المخزون، وإدارة العملاء والموردين في واجهة واحدة سريعة وبدون أي تعقيدات.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start gap-4">
                                <div class="rounded-md shadow">
                                    <Link :href="route('register')" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#714B67] hover:bg-[#5a3a52] md:py-4 md:text-lg md:px-10">
                                        ابدأ الآن مجاناً
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:left-0 lg:w-1/2 mt-10 lg:mt-0 flex items-center justify-center bg-gray-50">
                <!-- Abstract Hero Graphic placeholder -->
                <div class="p-10 w-full h-full flex items-center justify-center relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#017E84]/10 to-[#714B67]/10 rounded-l-3xl"></div>
                    <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80" alt="Dashboard Preview" class="rounded-lg shadow-2xl z-10 w-3/4 object-cover h-3/4 border-4 border-white" />
                </div>
            </div>
        </div>

        <!-- Features / Apps Grid (Odoo Style) -->
        <div id="features" class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl">
                        الوحدات الأساسية للنظام المبسط
                    </h2>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                        كل ما تحتاجه لإدارة شركتك الصغيرة بفعالية تامة.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <!-- App Card 1: Sales -->
                    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition cursor-pointer border border-gray-100">
                        <div class="w-16 h-16 mx-auto bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">المبيعات</h3>
                        <p class="text-sm text-gray-500 mt-2">إنشاء فواتير المبيعات وإدارتها بسهولة.</p>
                    </div>
                    <!-- App Card 2: Purchases -->
                    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition cursor-pointer border border-gray-100">
                        <div class="w-16 h-16 mx-auto bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">المشتريات</h3>
                        <p class="text-sm text-gray-500 mt-2">تسجيل المشتريات من الموردين.</p>
                    </div>
                    <!-- App Card 3: Inventory -->
                    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition cursor-pointer border border-gray-100">
                        <div class="w-16 h-16 mx-auto bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">المخزون</h3>
                        <p class="text-sm text-gray-500 mt-2">تتبع فوري للأصناف والكميات.</p>
                    </div>
                    <!-- App Card 4: Accounts -->
                    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition cursor-pointer border border-gray-100">
                        <div class="w-16 h-16 mx-auto bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">الحسابات والصناديق</h3>
                        <p class="text-sm text-gray-500 mt-2">أرصدة العملاء والموردين وسندات القبض والصرف.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-gray-500 text-sm">
                    &copy; 2026 نظام الحسابات المبسط. جميع الحقوق محفوظة.
                </p>
            </div>
        </footer>
    </div>
</template>
