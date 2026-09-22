<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import axios from 'axios';

const form = useForm({
    uname: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    company_name: '',
    company_code: '',
});

// Subdomain checking logic
const subdomainStatus = ref('empty'); // empty, checking, available, taken
let typingTimer;

watch(() => form.company_code, (newVal) => {
    clearTimeout(typingTimer);
    
    if (!newVal || newVal.trim() === '') {
        subdomainStatus.value = 'empty';
        return;
    }
    
    // Automatically sanitize input to be slug-like
    form.company_code = newVal.replace(/[^a-zA-Z0-9-]/g, '').toLowerCase();
    
    if (form.company_code.length === 0) {
        subdomainStatus.value = 'empty';
        return;
    }

    subdomainStatus.value = 'checking';
    
    typingTimer = setTimeout(async () => {
        try {
            const response = await axios.get(`/api/check-subdomain?code=${form.company_code}`);
            if (response.data.available) {
                subdomainStatus.value = 'available';
            } else {
                subdomainStatus.value = 'taken';
            }
        } catch (e) {
            console.error(e);
            subdomainStatus.value = 'empty'; // fallback
        }
    }, 500); // 500ms debounce
});

// Password confirmation matching logic
const passwordMatchStatus = computed(() => {
    if (!form.password_confirmation || !form.password) return 'empty';
    if (form.password === form.password_confirmation) return 'match';
    return 'mismatch';
});

const isFormValid = computed(() => {
    return subdomainStatus.value !== 'taken' && 
           (passwordMatchStatus.value === 'match' || passwordMatchStatus.value === 'empty');
});

const submit = () => {
    if (subdomainStatus.value === 'taken') {
        alert('النطاق الفرعي محجوز، يرجى اختيار نطاق آخر.');
        return;
    }
    if (passwordMatchStatus.value === 'mismatch') {
        alert('كلمتا المرور غير متطابقتين.');
        return;
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="تسجيل حساب مؤسسة جديد" />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 dir-rtl">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <Link :href="route('welcome')" class="text-3xl font-bold text-[#714B67]">كودي إي آر بي</Link>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                تسجيل شركتك وحساب جديد
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                أو
                <Link :href="route('login')" class="font-medium text-[#017E84] hover:text-teal-600">
                    تسجيل الدخول إذا كان لديك حساب
                </Link>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" @submit.prevent="submit">
                    <div v-if="form.errors.error" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-red-700 text-right">{{ form.errors.error }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 text-right">
                            اسم الشركة أو المؤسسة
                        </label>
                        <div class="mt-1">
                            <input id="company_name" v-model="form.company_name" type="text" required autofocus
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.company_name" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.company_name }}</p>
                    </div>

                    <div>
                        <label for="company_code" class="block text-sm font-medium text-gray-700 text-right">
                            النطاق الفرعي للشركة (Subdomain)
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm" dir="ltr">
                            <input id="company_code" v-model="form.company_code" type="text" required
                                :class="[
                                    'flex-1 appearance-none block w-full px-3 py-2 border rounded-l-md placeholder-gray-400 focus:outline-none focus:ring-[#017E84] sm:text-sm text-right transition-colors',
                                    subdomainStatus === 'taken' ? 'border-red-400 focus:border-red-500 bg-red-50' : 
                                    subdomainStatus === 'available' ? 'border-emerald-400 focus:border-emerald-500 bg-emerald-50' : 
                                    'border-gray-300 focus:border-[#017E84]'
                                ]"
                                placeholder="mycompany" />
                            <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                .localhost
                            </span>
                        </div>
                        
                        <!-- Real-time Subdomain Feedback -->
                        <div class="mt-2 text-sm flex items-center justify-end h-5">
                            <span v-if="subdomainStatus === 'checking'" class="text-blue-600 flex items-center gap-1">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                جاري التحقق...
                            </span>
                            <span v-else-if="subdomainStatus === 'available'" class="text-emerald-600 font-medium flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                هذا النطاق متاح!
                            </span>
                            <span v-else-if="subdomainStatus === 'taken'" class="text-red-600 font-medium flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                هذا النطاق محجوز، اختر اسماً آخر.
                            </span>
                        </div>
                        <p v-if="form.errors.company_code" class="mt-1 text-sm text-red-600 text-right">{{ form.errors.company_code }}</p>
                    </div>

                    <div>
                        <label for="uname" class="block text-sm font-medium text-gray-700 text-right">
                            الاسم بالكامل
                        </label>
                        <div class="mt-1">
                            <input id="uname" v-model="form.uname" type="text" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.uname" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.uname }}</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 text-right">
                            البريد الإلكتروني
                        </label>
                        <div class="mt-1">
                            <input id="email" v-model="form.email" type="email" autocomplete="email" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 text-right">
                            رقم الهاتف
                        </label>
                        <div class="mt-1">
                            <input id="phone" v-model="form.phone" type="tel" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.phone" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.phone }}</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 text-right">
                            كلمة المرور
                        </label>
                        <div class="mt-1">
                            <input id="password" v-model="form.password" type="password" autocomplete="new-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 text-right">
                            تأكيد كلمة المرور
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" required
                                :class="[
                                    'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] sm:text-sm text-right transition-colors',
                                    passwordMatchStatus === 'mismatch' ? 'border-red-400 focus:border-red-500 bg-red-50' : 
                                    passwordMatchStatus === 'match' ? 'border-emerald-400 focus:border-emerald-500 bg-emerald-50' : 
                                    'border-gray-300 focus:border-[#017E84]'
                                ]" />
                        </div>
                        
                        <!-- Real-time Password Feedback -->
                        <div class="mt-2 text-sm flex items-center justify-end h-5">
                            <span v-if="passwordMatchStatus === 'match'" class="text-emerald-600 font-medium flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                كلمتا المرور متطابقتان
                            </span>
                            <span v-else-if="passwordMatchStatus === 'mismatch'" class="text-red-600 font-medium flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                كلمتا المرور غير متطابقتين!
                            </span>
                        </div>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing || !isFormValid"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#017E84] hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#017E84] disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                            إنشاء الشركة وحسابي
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
