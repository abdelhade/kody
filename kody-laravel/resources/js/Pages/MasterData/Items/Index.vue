<script setup>
import { ref } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyCard from '@/Components/KodyCard.vue';
import KodyButton from '@/Components/KodyButton.vue';
import KodyDataGrid from '@/Components/KodyDataGrid.vue';
import KodyInput from '@/Components/KodyInput.vue';

const props = defineProps({
  items: Object
});

const page = usePage();
const can = (permission) => page.props.auth?.permissions?.includes(permission) || true;

const form = useForm({});

const deleteItem = (id) => {
  if (confirm('هل أنت متأكد من حذف هذا الصنف؟')) {
    form.delete(`/master-data/items/${id}`);
  }
};

const tableColumns = [
    { key: 'id', label: 'م', sortable: true, width: '5%' },
    { key: 'iname', label: 'اسم الصنف', sortable: true, width: '30%' },
    { key: 'group', label: 'المجموعة', sortable: true, width: '20%' },
    { key: 'price1', label: 'سعر البيع (الأساسي)', sortable: true, width: '15%' },
    { key: 'itmqty', label: 'الرصيد الكلي', sortable: true, width: '15%' },
    { key: 'actions', label: 'الخيارات', sortable: false, width: '15%' }
];
</script>

<template>
  <TopNavbarLayout title="إدارة الأصناف">
    <template #header>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">إدارة الأصناف (المنتجات)</h1>
                <p class="text-sm text-gray-500 mt-1">إضافة وتعديل بيانات الأصناف، الأسعار، الوحدات، والباركودات</p>
            </div>
            <Link v-if="can('create_item')" href="/master-data/items/create" class="inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 bg-[#017E84] text-white hover:bg-[#01686d] focus:ring-[#017E84] px-4 py-2 text-base">
                + صنف جديد
            </Link>
        </div>
    </template>

    <KodyCard noPadding>
        <div class="p-4 border-b border-gray-100 flex gap-4 bg-gray-50/30">
            <div class="w-1/3">
                <KodyInput placeholder="بحث بالاسم أو الباركود..." class="!mb-0" />
            </div>
        </div>

        <KodyDataGrid 
            :columns="tableColumns" 
            :data="items.data"
        >
            <template #cell-group="{ row }">
                <span v-if="row.group" class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-xs font-medium">
                    {{ row.group.gname }}
                </span>
                <span v-else class="text-gray-400">-</span>
            </template>

            <template #cell-price1="{ value }">
                <span class="text-emerald-600 font-bold font-mono">{{ value.toLocaleString() }}</span>
            </template>

            <template #cell-itmqty="{ value }">
                <span :class="value <= 0 ? 'text-red-600' : 'text-gray-700'" class="font-medium font-mono">
                    {{ value }}
                </span>
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center justify-start gap-2">
                    <Link v-if="can('update_item')" :href="`/master-data/items/${row.id}/edit`" class="text-[#017E84] hover:bg-[#017E84]/10 p-1.5 rounded transition" title="تعديل">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </Link>
                    <button v-if="can('delete_item')" @click="deleteItem(row.id)" class="text-red-600 hover:bg-red-50 p-1.5 rounded transition" title="حذف">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </template>
        </KodyDataGrid>
    </KodyCard>
  </TopNavbarLayout>
</template>
