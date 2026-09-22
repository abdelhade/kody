<template>
  <AppLayout>
    <div class="space-y-6">
      <div>
        <h2 class="text-2xl font-black text-slate-800">قائمة الدخل (الأرباح والخسائر)</h2>
        <p class="text-xs text-slate-500 mt-1">ملخص الإيرادات والمصروفات لتحديد صافي الربح أو الخسارة.</p>
      </div>

      <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-end gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">من تاريخ</label>
          <input type="date" v-model="form.from_date" class="border border-slate-300 rounded px-2 py-1.5 text-sm font-mono">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">إلى تاريخ</label>
          <input type="date" v-model="form.to_date" class="border border-slate-300 rounded px-2 py-1.5 text-sm font-mono">
        </div>
        <button @click="fetchReport" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded shadow-sm">تحديث</button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Revenues -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-4 border-b border-slate-100 bg-emerald-50 text-emerald-800 font-bold">
            <h3>الإيرادات والمبيعات</h3>
          </div>
          <table class="w-full text-right text-sm">
            <tbody class="divide-y divide-slate-100 text-xs">
              <tr v-for="r in reportData.revenues" :key="r.id" class="hover:bg-slate-50">
                <td class="py-2 px-4">{{ r.aname }}</td>
                <td class="py-2 px-4 font-mono font-bold text-emerald-600">{{ Number(Math.abs(r.balance)).toLocaleString('ar-EG') }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200 text-sm font-bold">
              <tr>
                <td class="py-3 px-4">إجمالي الإيرادات</td>
                <td class="py-3 px-4 text-emerald-700 font-mono">{{ Number(reportData.total_revenue).toLocaleString('ar-EG') }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Expenses -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-4 border-b border-slate-100 bg-red-50 text-red-800 font-bold">
            <h3>المصروفات وتكلفة المبيعات</h3>
          </div>
          <table class="w-full text-right text-sm">
            <tbody class="divide-y divide-slate-100 text-xs">
              <tr v-for="e in reportData.expenses" :key="e.id" class="hover:bg-slate-50">
                <td class="py-2 px-4">{{ e.aname }}</td>
                <td class="py-2 px-4 font-mono font-bold text-red-600">{{ Number(e.balance).toLocaleString('ar-EG') }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200 text-sm font-bold">
              <tr>
                <td class="py-3 px-4">إجمالي المصروفات</td>
                <td class="py-3 px-4 text-red-700 font-mono">{{ Number(reportData.total_expense).toLocaleString('ar-EG') }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Net Result -->
      <div class="bg-slate-800 rounded-xl p-6 text-center shadow-lg text-white">
        <h3 class="text-slate-300 text-sm font-bold mb-2">صافي النتيجة (ربح / خسارة)</h3>
        <p :class="['text-4xl font-black font-mono', reportData.net_profit >= 0 ? 'text-emerald-400' : 'text-red-400']">
          {{ reportData.net_profit >= 0 ? '+' : '-' }}{{ Number(Math.abs(reportData.net_profit)).toLocaleString('ar-EG') }} ج.م
        </p>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  reportData: Object,
  filters: Object,
});

const form = ref({
  from_date: props.filters?.from_date || '',
  to_date: props.filters?.to_date || '',
});

function fetchReport() {
  router.get('/reports/profit-loss', form.value, { preserveState: true });
}
</script>
