<template>
  <AppLayout>
    <div class="space-y-6">
      <div>
        <h2 class="text-2xl font-black text-slate-800">ميزان المراجعة</h2>
        <p class="text-xs text-slate-500 mt-1">توازن جميع الحسابات (أرصدة ومجاميع) للتأكد من صحة التوجيه المحاسبي.</p>
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

      <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm">
            <thead class="bg-slate-100 border-b border-slate-200 text-xs font-bold text-slate-600">
              <tr>
                <th class="py-2 px-4" rowspan="2">رقم الحساب</th>
                <th class="py-2 px-4" rowspan="2">اسم الحساب</th>
                <th class="py-1 px-4 text-center border-b border-slate-200" colspan="2">المجاميع</th>
                <th class="py-1 px-4 text-center border-b border-slate-200" colspan="2">الأرصدة</th>
              </tr>
              <tr>
                <th class="py-2 px-4 text-emerald-600 border-r border-slate-200">مدين</th>
                <th class="py-2 px-4 text-red-600">دائن</th>
                <th class="py-2 px-4 text-emerald-600 border-r border-slate-200">أرصدة مدينة</th>
                <th class="py-2 px-4 text-red-600">أرصدة دائنة</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-mono">
              <tr v-for="b in balances" :key="b.id" class="hover:bg-slate-50/50">
                <td class="py-2 px-4">{{ b.id }}</td>
                <td class="py-2 px-4 font-bold text-slate-800 font-sans">{{ b.aname }}</td>
                <td class="py-2 px-4 text-emerald-600 border-r border-slate-50">{{ Number(b.total_debit || 0).toLocaleString('ar-EG') }}</td>
                <td class="py-2 px-4 text-red-600">{{ Number(b.total_credit || 0).toLocaleString('ar-EG') }}</td>
                <td class="py-2 px-4 text-emerald-600 font-bold border-r border-slate-50">{{ b.balance > 0 ? Number(b.balance).toLocaleString('ar-EG') : '-' }}</td>
                <td class="py-2 px-4 text-red-600 font-bold">{{ b.balance < 0 ? Number(Math.abs(b.balance)).toLocaleString('ar-EG') : '-' }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-sm font-mono">
              <tr>
                <td colspan="2" class="py-3 px-4 font-sans text-left">الإجماليات الكلية</td>
                <td class="py-3 px-4 text-emerald-700 border-r border-slate-200">{{ Number(balances.reduce((s, a) => s + Number(a.total_debit), 0)).toLocaleString('ar-EG') }}</td>
                <td class="py-3 px-4 text-red-700">{{ Number(balances.reduce((s, a) => s + Number(a.total_credit), 0)).toLocaleString('ar-EG') }}</td>
                <td class="py-3 px-4 text-emerald-700 border-r border-slate-200">{{ Number(balances.filter(a => a.balance > 0).reduce((s, a) => s + Number(a.balance), 0)).toLocaleString('ar-EG') }}</td>
                <td class="py-3 px-4 text-red-700">{{ Number(balances.filter(a => a.balance < 0).reduce((s, a) => s + Math.abs(Number(a.balance)), 0)).toLocaleString('ar-EG') }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  balances: Array,
  filters: Object,
});

const form = ref({
  from_date: props.filters?.from_date || '',
  to_date: props.filters?.to_date || '',
});

function fetchReport() {
  router.get('/reports/trial-balance', form.value, { preserveState: true });
}
</script>
