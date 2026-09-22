<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const currentStep = ref(0);
const steps = [
    "جاري إنشاء قاعدة البيانات الخاصة بشركتك...",
    "جاري تهيئة الجداول الأساسية...",
    "جاري إنشاء حساب الإدارة الخاص بك...",
    "يتم الآن إنهاء الإعدادات وتأمين البيانات...",
    "أوشكنا على الانتهاء... جاري تحضير لوحة التحكم..."
];

onMounted(() => {
    // Cycle through steps every 2.5 seconds to give user feedback
    const stepInterval = setInterval(() => {
        if (currentStep.value < steps.length - 1) {
            currentStep.value++;
        }
    }, 2500);

    // Automatically call the provision endpoint
    axios.post(route('workspaces.store'))
        .then(response => {
            clearInterval(stepInterval);
            currentStep.value = steps.length - 1; // Show last step
            if (response.data.url) {
                setTimeout(() => {
                    window.location.href = response.data.url;
                }, 500);
            }
        })
        .catch(error => {
            clearInterval(stepInterval);
            console.error('Error provisioning workspace', error);
            alert('حدث خطأ أثناء إعداد شركتك. يرجى المحاولة مرة أخرى.');
            // Optionally redirect back or allow retry
            window.location.href = '/register';
        });
});
</script>

<template>
    <Head title="جاري إنشاء بيئة العمل..." />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 dir-rtl">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                جاري إعداد مساحة عملك...
            </h2>
            
            <p class="mt-4 text-center text-lg text-[#017E84] font-semibold transition-all duration-500 ease-in-out">
                {{ steps[currentStep] }}
            </p>

            <div class="mt-8 flex justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-12 w-12 text-[#017E84]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <p class="mt-6 text-center text-sm text-gray-500">
                يرجى عدم إغلاق هذه الصفحة أو تحديثها.
            </p>
        </div>
    </div>
</template>
