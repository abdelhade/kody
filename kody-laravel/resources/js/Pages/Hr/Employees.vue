<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-800">شؤون الموظفين</h2>
          <p class="text-xs text-slate-500 mt-1">إدارة الموظفين، الأقسام، والمسميات الوظيفية.</p>
        </div>
        <div>
          <button class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md cursor-pointer">
            <span>➕ موظف جديد</span>
          </button>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500">
              <tr>
                <th class="py-3 px-4">رقم</th>
                <th class="py-3 px-4">الاسم</th>
                <th class="py-3 px-4">القسم</th>
                <th class="py-3 px-4">الوظيفة</th>
                <th class="py-3 px-4">الراتب الأساسي</th>
                <th class="py-3 px-4 text-center">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              <tr v-for="emp in employees.data" :key="emp.id" class="hover:bg-slate-50/50">
                <td class="py-3 px-4 font-mono font-bold">{{ emp.id }}</td>
                <td class="py-3 px-4 font-bold text-slate-800">{{ emp.name }}</td>
                <td class="py-3 px-4 text-slate-600">{{ emp.department_rel?.name || '-' }}</td>
                <td class="py-3 px-4 text-slate-600">{{ emp.job_rel?.name || '-' }}</td>
                <td class="py-3 px-4 font-mono font-bold text-emerald-600">{{ Number(emp.salary || 0).toLocaleString('ar-EG') }} ج.م</td>
                <td class="py-3 px-4 text-center">
                  <button class="text-blue-600 hover:text-blue-800 font-bold px-2">تعديل</button>
                </td>
              </tr>
              <tr v-if="!employees.data.length">
                <td colspan="6" class="py-8 text-center text-slate-400">لا يوجد موظفين حالياً</td>
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
  employees: Object,
  departments: Array,
  jobs: Array
});
</script>
