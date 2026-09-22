<template>
    <Head title="مساحة العمل جاهزة" />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 dir-rtl">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900 mb-2">
                مساحة عملك جاهزة ومفعلة!
            </h2>
            <p class="text-gray-500 mb-8">
                لقد تم إنشاء شركتك بنجاح. يمكنك الآن تسجيل الدخول إلى لوحة التحكم الخاصة بك.
            </p>

            <div class="flex flex-col gap-3">
                <a :href="url" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#017E84] hover:bg-[#016A6F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#017E84] transition-colors">
                    الذهاب إلى تسجيل الدخول
                </a>
                
                <a href="/logout" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#017E84] transition-colors">
                    تسجيل الخروج وإنشاء حساب جديد
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    url: String
});
</script>
