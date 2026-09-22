<template>
  <TopNavbarLayout>
    <div class="max-w-6xl mx-auto p-6 space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <h2 class="text-2xl font-black text-slate-800">إدارة الورديات وتقفيل Z-Report</h2>
          <p class="text-sm text-slate-500 mt-1">متابعة مبيعات الكاشير اللحظية، تقفيل الوردية، ومطابقة العجز والزيادة في النقدية.</p>
        </div>
        <div>
          <KodyButton
            @click="showCloseModal = true"
            icon="CheckBadgeIcon"
          >
            تقفيل الوردية الحالية (تقرير Z)
          </KodyButton>
        </div>
      </div>

      <!-- Current Live Shift Banner -->
      <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-md">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div>
            <div class="flex items-center gap-2 mb-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping absolute"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 relative"></span>
              <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">الوردية الحالية مفتوحة</span>
              <span class="text-xs text-slate-400 font-mono">({{ currentShiftStats.date }})</span>
            </div>
            <h3 class="text-xl font-black">مبيعات الوردية الإجمالية الآن</h3>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-center">
            <div class="bg-white/5 border border-white/10 rounded-xl p-3">
              <span class="text-xs text-slate-400">عدد الطلبات</span>
              <p class="text-2xl font-black text-white mt-1 font-mono">{{ currentShiftStats.total_orders }}</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-3">
              <span class="text-xs text-slate-400">إجمالي المبيعات</span>
              <p class="text-2xl font-black text-emerald-400 mt-1 font-mono">
                {{ Number(currentShiftStats.total_sales).toLocaleString('ar-EG') }} ج.م
              </p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-3 col-span-2 sm:col-span-1">
              <span class="text-xs text-slate-400">النقدية المحصلة</span>
              <p class="text-2xl font-black text-blue-400 mt-1 font-mono">
                {{ Number(currentShiftStats.total_cash).toLocaleString('ar-EG') }} ج.م
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Closed Shifts History -->
      <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
          <h3 class="font-bold text-slate-800 text-base">سجل الورديات المغلقة السابقة</h3>
          <span class="text-xs text-slate-400">إجمالي السجلات: {{ closedShifts.total || 0 }}</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500">
              <tr>
                <th class="py-3 px-4">رقم الوردية</th>
                <th class="py-3 px-4">التاريخ والوقت</th>
                <th class="py-3 px-4">الكاشير</th>
                <th class="py-3 px-4">إجمالي المبيعات</th>
                <th class="py-3 px-4">المصروفات</th>
                <th class="py-3 px-4">النقدية الفعلية</th>
                <th class="py-3 px-4">العجز / الزيادة</th>
                <th class="py-3 px-4 text-center">إجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              <tr v-for="s in closedShifts.data" :key="s.id" class="hover:bg-slate-50/50">
                <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ s.shift }}</td>
                <td class="py-3 px-4 text-slate-500 font-mono">
                  {{ s.date }} <span class="text-[11px] text-slate-400">{{ s.endtime }}</span>
                </td>
                <td class="py-3 px-4 font-bold text-slate-700">{{ s.user }}</td>
                <td class="py-3 px-4 font-mono font-bold text-emerald-600">
                  {{ Number(s.total_sales).toLocaleString('ar-EG') }} ج.م
                </td>
                <td class="py-3 px-4 font-mono text-red-600">
                  {{ Number(s.expenses || 0).toLocaleString('ar-EG') }} ج.م
                </td>
                <td class="py-3 px-4 font-mono font-bold text-slate-800">
                  {{ Number(s.actual_cash || s.cash || 0).toLocaleString('ar-EG') }} ج.م
                </td>
                <td class="py-3 px-4 font-mono font-bold">
                  <span
                    :class="[
                      'inline-block px-2 py-0.5 rounded text-xs',
                      s.deficit < 0 ? 'bg-red-50 text-red-700' : (s.deficit > 0 ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700')
                    ]"
                  >
                    {{ s.deficit < 0 ? `عجز: ${Number(Math.abs(s.deficit)).toLocaleString('ar-EG')}` : (s.deficit > 0 ? `زيادة: ${Number(s.deficit).toLocaleString('ar-EG')}` : 'مضبوط ✓') }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <button
                    @click="printShift(s)"
                    class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded text-[11px] cursor-pointer"
                  >
                    🖨️ تقرير Z
                  </button>
                </td>
              </tr>
              <tr v-if="!closedShifts.data || closedShifts.data.length === 0">
                <td colspan="8" class="py-10 text-center text-slate-400">لا توجد ورديات مغلقة مسجلة حتى الآن</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Close Shift Modal -->
      <div
        v-if="showCloseModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
      >
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" dir="rtl">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-black text-slate-800">تقفيل الوردية وإصدار تقرير Z</h3>
            <button @click="showCloseModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
          </div>

          <form @submit.prevent="submitCloseShift" class="space-y-3">
            <div class="bg-slate-50 p-3 rounded-xl space-y-1 text-xs">
              <div class="flex justify-between text-slate-600">
                <span>إجمالي مبيعات الوردية:</span>
                <span class="font-mono font-bold">{{ Number(currentShiftStats.total_sales).toLocaleString('ar-EG') }} ج.م</span>
              </div>
              <div class="flex justify-between text-slate-600">
                <span>المصروفات النثرية:</span>
                <span class="font-mono font-bold text-red-600">{{ Number(form.expenses || 0).toLocaleString('ar-EG') }} ج.م</span>
              </div>
              <div class="flex justify-between text-slate-800 font-black border-t border-slate-200 pt-1 text-sm">
                <span>النقدية المفترضة بالدرج:</span>
                <span class="font-mono text-emerald-600">
                  {{ Number(currentShiftStats.total_sales - (form.expenses || 0)).toLocaleString('ar-EG') }} ج.م
                </span>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ الفعلي الموجود بالدرج (الجرد) *</label>
              <input
                v-model.number="form.actual_cash"
                type="number"
                step="any"
                required
                class="w-full px-3 py-2 text-base font-mono font-bold border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-center"
                placeholder="0.00"
              />
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">مصروفات من الدرج</label>
                <input
                  v-model.number="form.expenses"
                  type="number"
                  step="any"
                  min="0"
                  class="w-full px-3 py-1.5 text-xs font-mono border border-slate-200 rounded-lg focus:outline-none"
                  placeholder="0.00"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">بيان المصروفات</label>
                <input
                  v-model="form.exp_notes"
                  type="text"
                  class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none"
                  placeholder="نثريات / شاي"
                />
              </div>
            </div>

            <div v-if="form.actual_cash > 0" class="p-3 rounded-xl text-xs font-bold flex justify-between items-center" :class="diffAmount < 0 ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'">
              <span>نتيجة المطابقة:</span>
              <span class="font-mono text-sm">
                {{ diffAmount < 0 ? `عجز قدره: ${Number(Math.abs(diffAmount)).toLocaleString('ar-EG')} ج.م` : (diffAmount > 0 ? `زيادة قدرها: ${Number(diffAmount).toLocaleString('ar-EG')} ج.م` : 'النقدية مطابقة تماماً ✓') }}
              </span>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ملاحظات إضافية</label>
              <textarea
                v-model="form.info"
                rows="2"
                class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none"
                placeholder="أي ملاحظات تخص الوردية..."
              ></textarea>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showCloseModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg cursor-pointer"
              >
                إلغاء
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-md cursor-pointer disabled:opacity-50"
              >
                {{ isSubmitting ? 'جاري الإغلاق...' : 'تأكيد إغلاق الوردية ✓' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </TopNavbarLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyButton from '@/Components/KodyButton.vue';
import KodyModal from '@/Components/KodyModal.vue';
import KodyInput from '@/Components/KodyInput.vue';

const props = defineProps({
  closedShifts: Object,
  currentShiftStats: Object,
});

const showCloseModal = ref(false);
const isSubmitting = ref(false);

const form = ref({
  actual_cash: props.currentShiftStats?.total_sales || 0,
  expenses: 0,
  exp_notes: '',
  info: '',
});

const diffAmount = computed(() => {
  const expected = (props.currentShiftStats?.total_sales || 0) - (form.value.expenses || 0);
  return (form.value.actual_cash || 0) - expected;
});

function submitCloseShift() {
  isSubmitting.value = true;
  router.post('/shifts/close', form.value, {
    onSuccess: () => {
      showCloseModal.value = false;
      isSubmitting.value = false;
    },
    onError: () => {
      isSubmitting.value = false;
    },
  });
}

function printShift(shift) {
  const receipt = `
==============================
      تقرير وردية (Z-REPORT)
==============================
رقم الوردية: ${shift.shift}
التاريخ: ${shift.date} ${shift.endtime || ''}
الكاشير: ${shift.user}
------------------------------
إجمالي المبيعات: ${Number(shift.total_sales).toFixed(2)} ج.م
المصروفات: ${Number(shift.expenses).toFixed(2)} ج.م
النقدية الفعلية: ${Number(shift.actual_cash || shift.cash).toFixed(2)} ج.م
الفارق: ${shift.deficit >= 0 ? '+' : ''}${Number(shift.deficit).toFixed(2)} ج.م
==============================
        كودي ERP & POS
==============================
  `;
  const w = window.open('', '', 'width=350,height=500');
  w.document.write(`<pre style="font-family: monospace; font-size: 13px; text-align: center;">${receipt}</pre>`);
  w.document.close();
  w.print();
}
</script>
