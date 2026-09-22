<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { UserCircleIcon, ChevronDownIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    title: String,
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col font-sans dir-rtl">
        <!-- Top Navbar (Sticky) -->
        <nav class="bg-[#017E84] text-white shadow-md sticky top-0 z-40">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-14">
                    
                    <!-- Right Side: Logo & Main Menu -->
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <div class="flex-shrink-0 flex items-center font-bold text-xl tracking-wider">
                            <Link href="/">Kody ERP</Link>
                        </div>

                        <!-- Menu Items -->
                        <div class="hidden md:flex items-center space-x-2 space-x-reverse mr-8">
                            <Menu as="div" class="relative inline-block text-right">
                                <MenuButton class="inline-flex w-full justify-center rounded-md px-4 py-2 text-sm font-medium text-white hover:bg-white/10 focus:outline-none">
                                    البيانات الأساسية
                                    <ChevronDownIcon class="mr-2 -ml-1 h-5 w-5 text-white" aria-hidden="true" />
                                </MenuButton>
                                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <div class="px-1 py-1">
                                            <MenuItem v-slot="{ active }"><Link href="/customers" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">العملاء</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/suppliers" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">الموردين</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/cashboxes" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">الصناديق</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/banks" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">البنوك</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/other-debtors" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">المدينين الآخرين</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/other-creditors" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">الدائنين الآخرين</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/assets" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">الأصول</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/revenues" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">الإيرادات</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/expenses" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">المصروفات</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/warehouses" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">المخازن</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/item-groups" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">مجموعات الأصناف</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/items" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">الأصناف</Link></MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>

                            <Link href="/invoices" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-white/10 transition">الفواتير</Link>
                            <Link href="/pos" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-white/10 transition">نقطة البيع POS</Link>
                            
                            <Menu as="div" class="relative inline-block text-right">
                                <MenuButton class="inline-flex w-full justify-center rounded-md px-4 py-2 text-sm font-medium text-white hover:bg-white/10 focus:outline-none">
                                    المالية والحسابات
                                    <ChevronDownIcon class="mr-2 -ml-1 h-5 w-5 text-white" aria-hidden="true" />
                                </MenuButton>
                                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <div class="px-1 py-1">
                                            <MenuItem v-slot="{ active }"><Link href="/vouchers" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">سندات القبض والصرف</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/accounting/opening-balances" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">الأرصدة الافتتاحية</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/shifts" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">تقفيل الورديات</Link></MenuItem>
                                        </div>
                                        <div class="px-1 py-1 border-t border-gray-100">
                                            <MenuItem v-slot="{ active }"><Link href="/reports/account-statement" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">كشف حساب</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/reports/trial-balance" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">ميزان المراجعة</Link></MenuItem>
                                            <MenuItem v-slot="{ active }"><Link href="/reports/profit-loss" :class="[active ? 'bg-[#017E84] text-white' : 'text-gray-900', 'block px-4 py-2 text-sm rounded-md']">أرباح وخسائر</Link></MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </div>
                    </div>

                    <!-- Left Side: User Profile / Settings -->
                    <div class="flex items-center">
                        <Menu as="div" class="relative inline-block text-left">
                            <MenuButton class="flex items-center rounded-full text-white hover:bg-white/10 p-1 focus:outline-none">
                                <UserCircleIcon class="h-8 w-8" aria-hidden="true" />
                                <span class="mr-2 text-sm font-medium hidden sm:block">{{ $page.props.auth?.user?.uname || 'User' }}</span>
                                <ChevronDownIcon class="mr-1 h-5 w-5 hidden sm:block" aria-hidden="true" />
                            </MenuButton>
                            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                <MenuItems class="absolute left-0 mt-2 w-48 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="px-1 py-1">
                                        <MenuItem v-slot="{ active }">
                                            <button :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-right px-4 py-2 text-sm rounded-md']">تغيير الثيم (قريباً)</button>
                                        </MenuItem>
                                        <MenuItem v-slot="{ active }">
                                            <Link href="/logout" method="post" as="button" :class="[active ? 'bg-red-50 text-red-700' : 'text-red-600', 'block w-full text-right px-4 py-2 text-sm rounded-md mt-1 border-t border-gray-100']">تسجيل الخروج</Link>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>

                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-1 w-full max-w-[100vw] overflow-x-hidden p-4 sm:p-6 lg:p-8">
            <!-- Optional Page Header -->
            <header v-if="$slots.header" class="mb-6">
                <slot name="header" />
            </header>

            <slot />
        </main>
    </div>
</template>

<style>
/* Base overrides for High Density UI */
html, body {
    direction: rtl;
    background-color: #f9fafb; /* gray-50 */
}
</style>
