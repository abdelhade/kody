<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyCard from '@/Components/KodyCard.vue';
import KodyButton from '@/Components/KodyButton.vue';
import KodyInput from '@/Components/KodyInput.vue';
import KodySearchableSelect from '@/Components/KodySearchableSelect.vue';

const props = defineProps({
    invoice: { type: Object, default: () => null },
    customers: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
    items: { type: Array, default: () => [] }, // pre-loaded or async fetched
});

const form = useForm({
    acc1: props.invoice?.acc1 || null,
    store_id: props.invoice?.store_id || 1,
    pro_date: props.invoice?.pro_date || new Date().toISOString().split('T')[0],
    order_type: props.invoice?.order_type || 'takeaway',
    info: props.invoice?.info || '',
    items: props.invoice?.items || [],
    fat_disc: props.invoice?.fat_disc || 0,
    fat_tax: props.invoice?.fat_tax || 0,
    paid_amount: props.invoice?.paid_amount || 0,
});

const addItem = () => {
    form.items.push({
        item_id: null,
        qty: 1,
        u_val: 1,
        price: 0,
        discount: 0
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const subTotal = computed(() => {
    return form.items.reduce((sum, item) => sum + (item.qty * item.price) - (item.discount || 0), 0);
});

const netTotal = computed(() => {
    return subTotal.value - Number(form.fat_disc) + Number(form.fat_tax);
});

const submit = () => {
    if (props.invoice) {
        form.put(`/invoices/${props.invoice.id}`);
    } else {
        form.post('/invoices');
    }
};

// Initial empty row
if (form.items.length === 0) {
    addItem();
}
</script>

<template>
  <TopNavbarLayout :title="invoice ? 'تعديل فاتورة' : 'إنشاء فاتورة مبيعات'">
    <template #header>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <Link href="/invoices" class="text-[#017E84] hover:underline mr-2">الفواتير</Link>
                    {{ invoice ? 'تعديل فاتورة' : 'إنشاء فاتورة مبيعات جديدة' }}
                </h1>
            </div>
            <KodyButton @click="submit" :loading="form.processing" variant="primary" class="shadow-sm">
                حفظ الفاتورة
            </KodyButton>
        </div>
    </template>

    <form @submit.prevent="submit" class="space-y-6 pb-20">
        <!-- Header Info -->
        <KodyCard title="البيانات الأساسية">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <KodyInput v-model="form.pro_date" type="date" label="تاريخ الفاتورة" required :error="form.errors.pro_date" />
                </div>
                <div>
                    <KodySearchableSelect 
                        v-model="form.acc1" 
                        :options="customers" 
                        displayKey="aname" 
                        valueKey="id" 
                        label="العميل" 
                        placeholder="اختر العميل..."
                        required 
                        :error="form.errors.acc1" 
                    />
                </div>
                <div>
                    <KodySearchableSelect 
                        v-model="form.store_id" 
                        :options="warehouses" 
                        displayKey="aname" 
                        valueKey="id" 
                        label="المستودع" 
                        placeholder="اختر المستودع..."
                        required 
                        :error="form.errors.store_id" 
                    />
                </div>
                <div class="md:col-span-3">
                    <KodyInput v-model="form.info" label="ملاحظات الفاتورة" :error="form.errors.info" />
                </div>
            </div>
        </KodyCard>

        <!-- Items Table -->
        <KodyCard title="تفاصيل الأصناف" noPadding>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 w-12">#</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 w-1/3">الصنف</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 w-24">الكمية</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 w-32">السعر</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 w-24">الخصم</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 w-32">الإجمالي</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <tr v-for="(item, index) in form.items" :key="index" class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-gray-500">{{ index + 1 }}</td>
                            <td class="px-4 py-2">
                                <KodySearchableSelect 
                                    v-model="item.item_id" 
                                    :options="items" 
                                    displayKey="iname" 
                                    valueKey="id" 
                                    placeholder="ابحث عن الصنف..."
                                    class="!mb-0"
                                    :error="form.errors[`items.${index}.item_id`]" 
                                />
                            </td>
                            <td class="px-4 py-2">
                                <input v-model.number="item.qty" type="number" min="0.01" step="0.01" class="w-full rounded-md border-gray-300 focus:border-[#017E84] focus:ring-[#017E84] sm:text-sm text-center font-mono" dir="ltr" />
                            </td>
                            <td class="px-4 py-2">
                                <input v-model.number="item.price" type="number" min="0" step="0.01" class="w-full rounded-md border-gray-300 focus:border-[#017E84] focus:ring-[#017E84] sm:text-sm text-center font-mono" dir="ltr" />
                            </td>
                            <td class="px-4 py-2">
                                <input v-model.number="item.discount" type="number" min="0" step="0.01" class="w-full rounded-md border-gray-300 focus:border-[#017E84] focus:ring-[#017E84] sm:text-sm text-center font-mono" dir="ltr" />
                            </td>
                            <td class="px-4 py-2 font-mono font-bold text-gray-800 text-left" dir="ltr">
                                {{ ((item.qty * item.price) - item.discount).toFixed(2) }}
                            </td>
                            <td class="px-4 py-2 text-left">
                                <button type="button" @click="removeItem(index)" class="text-red-500 hover:bg-red-50 p-1.5 rounded" title="حذف الصنف">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="p-3 border-t border-gray-100 bg-gray-50/50">
                    <button type="button" @click="addItem" class="text-sm font-medium text-[#017E84] hover:text-[#01686d] flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        إضافة صنف جديد
                    </button>
                </div>
            </div>
        </KodyCard>

        <!-- Totals & Payment -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <KodyCard title="المدفوعات">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ المدفوع (نقداً/شبكة)</label>
                        <input v-model.number="form.paid_amount" type="number" min="0" step="0.01" class="w-full rounded-md border-gray-300 focus:border-[#017E84] focus:ring-[#017E84] sm:text-lg text-center font-mono font-bold text-emerald-600 bg-emerald-50/30" dir="ltr" />
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                        <span class="font-medium text-gray-600">المتبقي (آجل):</span>
                        <span class="font-mono font-bold text-red-600 text-lg" dir="ltr">{{ Math.max(0, netTotal - form.paid_amount).toFixed(2) }}</span>
                    </div>
                </div>
            </KodyCard>

            <KodyCard title="الملخص المالي" class="bg-gray-50 border-gray-200">
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>الإجمالي الفرعي:</span>
                        <span class="font-mono" dir="ltr">{{ subTotal.toFixed(2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                        <span class="font-medium">الخصم الإضافي:</span>
                        <input v-model.number="form.fat_disc" type="number" min="0" step="0.01" class="w-32 rounded-md border-gray-300 focus:border-[#017E84] focus:ring-[#017E84] sm:text-sm text-center font-mono" dir="ltr" />
                    </div>
                    
                    <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                        <span class="font-medium">الضريبة المضافة:</span>
                        <input v-model.number="form.fat_tax" type="number" min="0" step="0.01" class="w-32 rounded-md border-gray-300 focus:border-[#017E84] focus:ring-[#017E84] sm:text-sm text-center font-mono" dir="ltr" />
                    </div>
                    
                    <div class="flex justify-between items-center border-t border-gray-300 pt-4 mt-2">
                        <span class="text-lg font-bold text-gray-800">الصافي النهائي:</span>
                        <span class="text-2xl font-black text-[#017E84] font-mono" dir="ltr">{{ netTotal.toFixed(2) }}</span>
                    </div>
                </div>
            </KodyCard>
        </div>
    </form>
  </TopNavbarLayout>
</template>
