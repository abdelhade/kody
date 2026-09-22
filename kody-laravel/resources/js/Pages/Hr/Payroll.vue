<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-800">مسير الرواتب</h2>
          <p class="text-xs text-slate-500 mt-1">احتساب واستعراض رواتب الموظفين.</p>
        </div>
      </div>

      <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-end gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">الشهر</label>
          <input type="month" :value="filterMonth" class="border border-slate-300 rounded px-2 py-1 text-sm font-mono" readonly>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h3 class="font-bold text-sm">الرواتب المعتمدة لهذا الشهر</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500">
              <tr>
                <th class="py-3 px-4">رقم</th>
                <th class="py-3 px-4">الموظف</th>
                <th class="py-3 px-4">أيام الحضور</th>
                <th class="py-3 px-4">الأساسي المستحق</th>
                <th class="py-3 px-4">المكافآت</th>
                <th class="py-3 px-4">الخصومات</th>
                <th class="py-3 px-4">الصافي</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              <tr v-for="pay in payrolls" :key="pay.id" class="hover:bg-slate-50/50">
                <td class="py-3 px-4 font-mono">{{ pay.id }}</td>
                <td class="py-3 px-4 font-bold">{{ pay.employee?.name }}</td>
                <td class="py-3 px-4 font-mono">{{ pay.workdays }} / {{ pay.alldays }}</td>
                <td class="py-3 px-4 font-mono text-slate-600">{{ Number(pay.entitle || 0).toLocaleString('ar-EG') }}</td>
                <td class="py-3 px-4 font-mono text-emerald-600">+{{ Number(pay.bonus || 0).toLocaleString('ar-EG') }}</td>
                <td class="py-3 px-4 font-mono text-red-600">-{{ Number((pay.deduction || 0) + (pay.insurance || 0) + (pay.tax || 0)).toLocaleString('ar-EG') }}</td>
                <td class="py-3 px-4 font-mono font-bold bg-green-50 text-green-800">{{ Number(pay.net_pay || 0).toLocaleString('ar-EG') }} ج.م</td>
              </tr>
              <tr v-if="!payrolls.length">
                <td colspan="7" class="py-8 text-center text-slate-400">لا يوجد رواتب معتمدة في هذا الشهر</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  payrolls: Array,
  employees: Array,
  filterMonth: String,
  previewData: Object
});
</script>
