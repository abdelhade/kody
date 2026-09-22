<template>
  <div class="min-h-screen bg-slate-50 flex font-sans text-slate-800" dir="rtl">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col transition-all duration-300 z-40 fixed h-full shadow-2xl">
      <!-- Logo Area -->
      <div class="h-16 flex items-center px-4 bg-slate-950 border-b border-slate-800 shadow-md">
        <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center font-black text-lg mr-2 shadow-lg shadow-blue-500/30">K</div>
        <div class="mr-3">
          <h1 class="text-md font-bold tracking-tight text-slate-100">كودي ERP</h1>
          <p class="text-[10px] text-slate-400 truncate w-36">{{ tenantName || 'الفرع الرئيسي' }}</p>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
        <!-- Main Dashboard -->
        <Link href="/dashboard" :class="navClass('/dashboard')">
          <svg class="w-5 h-5 ml-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
          لوحة القيادة
        </Link>
        
        <!-- POS Highlight Button -->
        <div class="py-2">
            <Link href="/pos" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-bold bg-emerald-500 text-white hover:bg-emerald-400 transition-colors shadow-md shadow-emerald-500/20">
            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            نقطة البيع (POS)
            </Link>
        </div>

        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-4 px-3">الأنظمة الأساسية</div>
        
        <!-- Master Data Accordion / Links -->
        <div class="space-y-1 mb-2">
          <div class="px-3 py-2 text-sm font-semibold text-slate-400 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
            البيانات الأساسية
          </div>
          <Link href="/master-data/customers" :class="navClass('/master-data/customers')" class="pl-10 pr-3 py-1.5 text-sm block">العملاء</Link>
          <Link href="/master-data/suppliers" :class="navClass('/master-data/suppliers')" class="pl-10 pr-3 py-1.5 text-sm block">الموردين</Link>
          <Link href="/master-data/warehouses" :class="navClass('/master-data/warehouses')" class="pl-10 pr-3 py-1.5 text-sm block">المخازن والمستودعات</Link>
          <Link href="/master-data/item-groups" :class="navClass('/master-data/item-groups')" class="pl-10 pr-3 py-1.5 text-sm block">مجموعات الأصناف</Link>
          <Link href="/master-data/items" :class="navClass('/master-data/items')" class="pl-10 pr-3 py-1.5 text-sm block">شجرة الأصناف</Link>
        </div>
        
        <Link href="/invoices" :class="navClass('/invoices')">
          <svg class="w-5 h-5 ml-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          الفواتير
        </Link>
        
        <Link href="/vouchers" :class="navClass('/vouchers')">
          <svg class="w-5 h-5 ml-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          السندات
        </Link>
        
        <Link href="/accounts" :class="navClass('/accounts')">
          <svg class="w-5 h-5 ml-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
          الحسابات
        </Link>
        
        <Link href="/reports" :class="navClass('/reports')">
          <svg class="w-5 h-5 ml-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          التقارير
        </Link>

        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-6 px-3">الإدارة</div>

        <Link href="/settings" :class="navClass('/settings')">
          <svg class="w-5 h-5 ml-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
          الإعدادات
        </Link>
        
        <Link href="/users" :class="navClass('/users')">
          <svg class="w-5 h-5 ml-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          المستخدمين
        </Link>
        
        <Link href="/contact" :class="navClass('/contact')">
          <svg class="w-5 h-5 ml-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
          تواصل معنا
        </Link>
      </nav>

      <!-- User Menu -->
      <div class="p-4 bg-slate-950 border-t border-slate-800">
        <div class="flex items-center w-full">
          <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-200">
            {{ userInitials }}
          </div>
          <div class="mr-3 flex-1 overflow-hidden">
            <p class="text-sm font-medium text-slate-200 truncate">{{ userName }}</p>
            <Link href="/logout" method="post" as="button" class="text-xs text-slate-500 hover:text-red-400 transition-colors">
              تسجيل الخروج
            </Link>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col mr-64 min-w-0 transition-all duration-300">
      
      <!-- Top Header Mobile (Hidden on Desktop) -->
      <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sticky top-0 z-30 lg:hidden">
        <div class="flex items-center">
          <h1 class="text-lg font-bold text-slate-800">كودي ERP</h1>
        </div>
        <button class="text-slate-500 hover:bg-slate-100 p-2 rounded-md">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-x-hidden">
        <slot />
      </main>
      
    </div>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const tenantName = computed(() => page.props.currentTenant?.name);
const userName = computed(() => page.props.auth?.user?.uname || 'مدير النظام');
const userInitials = computed(() => {
  const name = userName.value;
  return name ? name.substring(0, 2) : 'مد';
});

// Helper for active navigation link
const navClass = (path) => {
  const isActive = page.url.startsWith(path);
  return [
    'flex items-center px-3 py-2.5 rounded-lg text-sm transition-all duration-200 group',
    isActive 
      ? 'bg-blue-600 text-white shadow-md shadow-blue-900/50 font-semibold' 
      : 'text-slate-300 hover:bg-slate-800 hover:text-white font-medium'
  ];
};
</script>

<style>
/* Custom scrollbar for sidebar */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
  border-radius: 4px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
  background: #475569;
}
</style>
