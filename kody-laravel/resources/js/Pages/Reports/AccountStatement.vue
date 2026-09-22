<template>
  <AppLayout>
    <div class="space-y-6">
      <div>
        <h2 class="text-2xl font-black text-slate-800">كشف حساب تفصيلي</h2>
        <p class="text-xs text-slate-500 mt-1">عرض حركات المدين والدائن لأي حساب مع الرصيد التراكمي.</p>
      </div>

      <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-end gap-4">
        <div class="flex-1 max-w-xs">
          <label class="block text-xs font-bold text-slate-700 mb-1">الحساب</label>
          <select v-model="form.account_id" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm">
            <option value="">اختر الحساب...</option>
            <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.id }} - {{ acc.aname }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">من تاريخ</label>
          <input type="date" v-model="form.from_date" class="border border-slate-300 rounded px-2 py-1.5 text-sm font-mono">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">إلى تاريخ</label>
          <input type="date" v-model="form.to_date" class="border border-slate-300 rounded px-2 py-1.5 text-sm font-mono">
        </div>
        <button @click="fetchReport" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded shadow-sm">عرض</button>
      </div>

      <div v-if="statement" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h3 class="font-bold text-slate-800">كشف حساب: {{ statement.account.aname }}</h3>
          <span class="text-xs font-mono font-bold bg-slate-200 px-2 py-1 rounded">الرصيد الافتتاحي: {{ Number(statement.opening_balance).toLocaleString('ar-EG') }}</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm">
            <thead class="bg-slate-100 border-b border-slate-200 text-xs font-bold text-slate-600">
              <tr>
                <th class="py-2 px-4">التاريخ</th>
                <th class="py-2 px-4">رقم القيد</th>
                <th class="py-2 px-4">البيان</th>
                <th class="py-2 px-4 text-emerald-600">مدين (+)</th>
                <th class="py-2 px-4 text-red-600">دائن (-)</th>
                <th class="py-2 px-4">الرصيد</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-mono">
              <tr v-for="t in statement.transactions" :key="t.id" class="hover:bg-slate-50/50">
                <td class="py-2 px-4">{{ new Date(t.crtime).toLocaleDateString() }}</td>
                <td class="py-2 px-4">{{ t.id }}</td>
                <td class="py-2 px-4 text-slate-600 font-sans">{{ t.info || 'حركة مالية' }}</td>
                <td class="py-2 px-4 font-bold text-emerald-600">{{ Number(t.debit).toLocaleString('ar-EG') }}</td>
                <td class="py-2 px-4 font-bold text-red-600">{{ Number(t.credit).toLocaleString('ar-EG') }}</td>
                <td class="py-2 px-4 font-bold">{{ Number(t.running_balance).toLocaleString('ar-EG') }}</td>
              </tr>
              <tr v-if="!statement.transactions.length">
                <td colspan="6" class="py-8 text-center text-slate-400 font-sans">لا توجد حركات في هذه الفترة</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-sm font-mono">
              <tr>
                <td colspan="3" class="py-3 px-4 font-sans">الإجماليات</td>
                <td class="py-3 px-4 text-emerald-700">{{ Number(statement.total_debit).toLocaleString('ar-EG') }}</td>
                <td class="py-3 px-4 text-red-700">{{ Number(statement.total_credit).toLocaleString('ar-EG') }}</td>
                <td class="py-3 px-4 text-blue-700">{{ Number(statement.closing_balance).toLocaleString('ar-EG') }}</td>
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
  accounts: Array,
  filters: Object,
  statement: Object
});

const form = ref({
  account_id: props.filters?.account_id || '',
  from_date: props.filters?.from_date || '',
  to_date: props.filters?.to_date || '',
});

function fetchReport() {
  if (!form.value.account_id) return alert('الرجاء اختيار الحساب');
  router.get('/reports/account-statement', form.value, { preserveState: true });
}
</script>
