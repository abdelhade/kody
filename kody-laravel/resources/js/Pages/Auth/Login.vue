<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="تسجيل الدخول - كودي تك" />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 dir-rtl">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <Link :href="route('welcome')" class="text-3xl font-bold text-[#714B67]">نظام حسابات مبسط</Link>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                تسجيل الدخول إلى حسابك
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                أو
                <Link :href="route('register')" class="font-medium text-[#017E84] hover:text-teal-600">
                    إنشاء حساب جديد
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
                        <label for="email" class="block text-sm font-medium text-gray-700 text-right">
                            البريد الإلكتروني
                        </label>
                        <div class="mt-1">
                            <input id="email" v-model="form.email" type="email" autocomplete="email" required autofocus
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 text-right">
                            كلمة المرور
                        </label>
                        <div class="mt-1">
                            <input id="password" v-model="form.password" type="password" autocomplete="current-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" v-model="form.remember" name="remember-me" type="checkbox"
                                class="h-4 w-4 text-[#017E84] focus:ring-[#017E84] border-gray-300 rounded" />
                            <label for="remember-me" class="mr-2 block text-sm text-gray-900">
                                تذكرني
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#017E84] hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#017E84] disabled:opacity-50">
                            تسجيل الدخول
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
