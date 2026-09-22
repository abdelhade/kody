<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyCard from '@/Components/KodyCard.vue';
import KodyButton from '@/Components/KodyButton.vue';
import KodyDataGrid from '@/Components/KodyDataGrid.vue';
import KodyModal from '@/Components/KodyModal.vue';
import KodyInput from '@/Components/KodyInput.vue';

const props = defineProps({
  warehouses: Object
});

const page = usePage();

const can = (permission) => page.props.auth?.permissions?.includes(permission) || true;

const isModalOpen = ref(false);

const form = useForm({
  id: null,
  aname: '',
  address: '',
});

const openModal = (warehouse = null) => {
  if (warehouse) {
    form.id = warehouse.id;
    form.aname = warehouse.aname;
    form.address = warehouse.address || '';
  } else {
    form.reset();
    form.id = null;
  }
  form.clearErrors();
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submit = () => {
  if (form.id) {
    form.put(`/master-data/warehouses/${form.id}`, {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post('/master-data/warehouses', {
      onSuccess: () => closeModal(),
    });
  }
};

const deleteWarehouse = (id) => {
  if (confirm('هل أنت متأكد من حذف هذا المخزن؟ لا يمكن الحذف إذا كانت عليه حركات.')) {
    form.delete(`/master-data/warehouses/${id}`);
  }
};

const tableColumns = [
    { key: 'code', label: 'كود المخزن', sortable: true, width: '15%' },
    { key: 'aname', label: 'اسم المخزن', sortable: true, width: '45%' },
    { key: 'address', label: 'العنوان', sortable: true, width: '25%' },
    { key: 'actions', label: 'الخيارات', sortable: false, width: '15%' }
];
</script>

<template>
  <TopNavbarLayout title="إدارة المخازن">
    <template #header>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">إدارة المخازن والمستودعات</h1>
                <p class="text-sm text-gray-500 mt-1">تعريف المستودعات (تنشئ حسابات أصول متداولة آلياً)</p>
            </div>
            <KodyButton v-if="can('create_warehouse')" @click="openModal()" variant="primary">
                + مخزن جديد
            </KodyButton>
        </div>
    </template>

    <KodyCard noPadding>
        <KodyDataGrid 
            :columns="tableColumns" 
            :data="warehouses.data"
        >
            <template #cell-aname="{ value }">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    {{ value }}
                </div>
            </template>

            <template #cell-address="{ value }">
                <span>{{ value || '-' }}</span>
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center justify-start gap-2">
                    <button v-if="can('update_warehouse')" @click="openModal(row)" class="text-[#017E84] hover:bg-[#017E84]/10 p-1.5 rounded transition" title="تعديل">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <button v-if="can('delete_warehouse')" @click="deleteWarehouse(row.id)" class="text-red-600 hover:bg-red-50 p-1.5 rounded transition" title="حذف">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </template>
        </KodyDataGrid>
    </KodyCard>

    <KodyModal :show="isModalOpen" @close="closeModal" maxWidth="md" :title="form.id ? 'تعديل بيانات المخزن' : 'إضافة مخزن جديد'">
        <form @submit.prevent="submit" class="space-y-4">
            <KodyInput v-model="form.aname" label="اسم المخزن" required :error="form.errors.aname" autofocus />
            <KodyInput v-model="form.address" label="العنوان" :error="form.errors.address" />
        </form>

        <template #footer>
            <KodyButton @click="submit" :loading="form.processing" variant="primary" class="w-full sm:w-auto sm:mr-3">
                حفظ
            </KodyButton>
            <KodyButton @click="closeModal" variant="secondary" class="mt-3 w-full sm:mt-0 sm:w-auto">
                إلغاء
            </KodyButton>
        </template>
    </KodyModal>
  </TopNavbarLayout>
</template>
