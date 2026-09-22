<template>
  <TopNavbarLayout>
    <div class="max-w-6xl mx-auto p-6 space-y-6">
      <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <h2 class="text-2xl font-black text-gray-800">الأرصدة الافتتاحية</h2>
          <p class="text-sm text-gray-500 mt-1">أدخل الأرصدة الافتتاحية لجميع الحسابات والعملاء والموردين لمرة واحدة.</p>
        </div>
        <KodyButton @click="saveBalances" :disabled="form.processing" icon="CheckCircleIcon">
          حفظ الأرصدة
        </KodyButton>
      </div>

      <KodyCard>
        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm">
            <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 w-32">كود الحساب</th>
                <th class="px-4 py-3">اسم الحساب</th>
                <th class="px-4 py-3 w-48">طبيعة الحساب</th>
                <th class="px-4 py-3 w-64">الرصيد الافتتاحي</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(account, index) in form.balances" :key="account.id" class="hover:bg-blue-50/50 transition-colors group">
                <td class="px-4 py-3 font-mono text-gray-500">{{ account.code }}</td>
                <td class="px-4 py-3 font-bold text-gray-800">{{ account.aname }}</td>
                <td class="px-4 py-3">
                  <KodyBadge :type="account.nature === 1 ? 'success' : 'danger'">
                    {{ account.nature === 1 ? 'مدين (Debit)' : 'دائن (Credit)' }}
                  </KodyBadge>
                </td>
                <td class="px-4 py-3">
                  <KodyInput 
                    v-model="account.start_balance" 
                    type="number" 
                    step="0.01"
                    placeholder="0.00"
                    class="w-full"
                  />
                </td>
              </tr>
              <tr v-if="!form.balances.length">
                <td colspan="4" class="px-4 py-8 text-center text-gray-400 font-bold">لا يوجد حسابات فرعية لعرضها</td>
              </tr>
            </tbody>
          </table>
        </div>
      </KodyCard>
    </div>
  </TopNavbarLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyButton from '@/Components/KodyButton.vue';
import KodyCard from '@/Components/KodyCard.vue';
import KodyInput from '@/Components/KodyInput.vue';
import KodyBadge from '@/Components/KodyBadge.vue';

const props = defineProps({
  accounts: Array,
});

// Prepare the form data. Map negative DB balances to absolute positive for UI if nature == 2.
const initialBalances = props.accounts.map(acc => ({
  id: acc.id,
  code: acc.code,
  aname: acc.aname,
  nature: acc.nature,
  start_balance: acc.nature == 2 ? Math.abs(acc.start_balance) : parseFloat(acc.start_balance || 0),
}));

const form = useForm({
  balances: initialBalances,
});

const saveBalances = () => {
  form.post('/accounting/opening-balances', {
    preserveScroll: true,
  });
};
</script>
