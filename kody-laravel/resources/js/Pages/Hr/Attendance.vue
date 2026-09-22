<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-800">الحضور والانصراف</h2>
          <p class="text-xs text-slate-500 mt-1">سجل بصمات الموظفين اليومية.</p>
        </div>
      </div>

      <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">تاريخ اليوم</label>
          <input type="date" :value="filterDate" class="border border-slate-300 rounded px-2 py-1 text-sm font-mono" readonly>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500">
              <tr>
                <th class="py-3 px-4">رقم الحركة</th>
                <th class="py-3 px-4">الموظف</th>
                <th class="py-3 px-4">التاريخ</th>
                <th class="py-3 px-4">الوقت</th>
                <th class="py-3 px-4">النوع</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              <tr v-for="att in attendance" :key="att.id" class="hover:bg-slate-50/50">
                <td class="py-3 px-4 font-mono">{{ att.id }}</td>
                <td class="py-3 px-4 font-bold text-slate-800">{{ att.employee?.name }}</td>
                <td class="py-3 px-4 font-mono">{{ att.fpdate }}</td>
                <td class="py-3 px-4 font-mono">{{ att.fptime }}</td>
                <td class="py-3 px-4">
                  <span class="px-2 py-1 rounded text-[10px] font-bold" :class="att.fptybe === 1 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'">
                    {{ att.fptybe === 1 ? 'حضور' : 'انصراف' }}
                  </span>
                </td>
              </tr>
              <tr v-if="!attendance.length">
                <td colspan="5" class="py-8 text-center text-slate-400">لا يوجد حركات بصمة لهذا اليوم</td>
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
  attendance: Array,
  employees: Array,
  filterDate: String
});
</script>
