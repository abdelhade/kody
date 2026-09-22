<script setup>
import { Link } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyCard from '@/Components/KodyCard.vue';
import KodyButton from '@/Components/KodyButton.vue';
import KodyDataGrid from '@/Components/KodyDataGrid.vue';
import KodyInput from '@/Components/KodyInput.vue';

const props = defineProps({
    invoices: Object
});

const tableColumns = [
    { key: 'pro_num', label: 'رقم الفاتورة', sortable: true, width: '15%' },
    { key: 'pro_date', label: 'التاريخ', sortable: true, width: '15%' },
    { key: 'customer', label: 'العميل', sortable: true, width: '25%' },
    { key: 'fat_total', label: 'الإجمالي', sortable: true, width: '15%' },
    { key: 'payment_status', label: 'حالة الدفع', sortable: true, width: '15%' },
    { key: 'actions', label: 'الخيارات', sortable: false, width: '15%' }
];

const getStatusColor = (status) => {
    switch(status) {
        case 'paid': return 'bg-emerald-100 text-emerald-800';
        case 'unpaid': return 'bg-red-100 text-red-800';
        case 'partial': return 'bg-yellow-100 text-yellow-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getStatusLabel = (status) => {
    switch(status) {
        case 'paid': return 'مدفوعة';
        case 'unpaid': return 'غير مدفوعة (آجلة)';
        case 'partial': return 'مدفوعة جزئياً';
        default: return status || 'غير محدد';
    }
};
</script>

<template>
  <TopNavbarLayout title="فواتير المبيعات">
    <template #header>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">فواتير المبيعات</h1>
                <p class="text-sm text-gray-500 mt-1">إدارة فواتير المبيعات، المدفوعات، والتقارير</p>
            </div>
            <Link href="/invoices/create" class="inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 bg-[#017E84] text-white hover:bg-[#01686d] focus:ring-[#017E84] px-4 py-2 text-base shadow-sm">
                + فاتورة مبيعات جديدة
            </Link>
        </div>
    </template>

    <KodyCard noPadding>
        <div class="p-4 border-b border-gray-100 flex gap-4 bg-gray-50/30">
            <div class="w-1/3">
                <KodyInput placeholder="بحث برقم الفاتورة أو اسم العميل..." class="!mb-0" />
            </div>
        </div>

        <KodyDataGrid 
            :columns="tableColumns" 
            :data="invoices.data"
            emptyMessage="لا توجد فواتير مبيعات بعد."
        >
            <template #cell-pro_num="{ value }">
                <span class="font-mono font-medium text-gray-700">{{ value || '-' }}</span>
            </template>

            <template #cell-customer="{ row }">
                <span v-if="row.customer" class="font-medium">{{ row.customer.aname }}</span>
                <span v-else class="text-gray-400">عميل نقدي</span>
            </template>
            
            <template #cell-fat_total="{ value }">
                <span class="font-bold text-gray-800">{{ Number(value).toLocaleString() }} <span class="text-xs text-gray-500">ر.س</span></span>
            </template>

            <template #cell-payment_status="{ value }">
                <span :class="['px-2 py-1 rounded text-xs font-medium', getStatusColor(value)]">
                    {{ getStatusLabel(value) }}
                </span>
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center justify-start gap-2">
                    <button class="text-blue-600 hover:bg-blue-50 p-1.5 rounded transition" title="طباعة">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    </button>
                    <Link :href="`/invoices/${row.id}`" class="text-gray-600 hover:bg-gray-100 p-1.5 rounded transition" title="عرض">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </Link>
                </div>
            </template>
        </KodyDataGrid>
    </KodyCard>
  </TopNavbarLayout>
</template>
