<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyCard from '@/Components/KodyCard.vue';
import KodyButton from '@/Components/KodyButton.vue';
import KodyDataGrid from '@/Components/KodyDataGrid.vue';
import KodyModal from '@/Components/KodyModal.vue';
import KodyInput from '@/Components/KodyInput.vue';
import KodySearchableSelect from '@/Components/KodySearchableSelect.vue';

const props = defineProps({
  customers: Object
});

const page = usePage();

// Temporary helper for permissions
const can = (permission) => page.props.auth?.permissions?.includes(permission) || true;

const isModalOpen = ref(false);

const form = useForm({
  id: null,
  aname: '',
  phone: '',
  address: '',
  info: '', // tax number
  start_balance: 0,
  nature: 1, // 1 debit, 2 credit
  credit: 0
});

// For nature selection
const natureOptions = [
    { id: 1, name: 'مدين' },
    { id: 2, name: 'دائن' }
];

const openModal = (customer = null) => {
  if (customer) {
    form.id = customer.id;
    form.aname = customer.aname;
    form.phone = customer.phone || '';
    form.address = customer.address || '';
    form.info = customer.info || '';
    form.start_balance = Math.abs(customer.start_balance);
    form.nature = customer.start_balance < 0 ? 2 : 1;
    form.credit = customer.credit;
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
    form.put(`/master-data/customers/${form.id}`, {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post('/master-data/customers', {
      onSuccess: () => closeModal(),
    });
  }
};

const deleteCustomer = (id) => {
  if (confirm('هل أنت متأكد من حذف هذا العميل؟')) {
    form.delete(`/master-data/customers/${id}`);
  }
};

// DataGrid Columns Definition
const tableColumns = [
    { key: 'code', label: 'كود العميل', sortable: true, width: '15%' },
    { key: 'aname', label: 'اسم العميل', sortable: true, width: '25%' },
    { key: 'phone', label: 'رقم الجوال', sortable: true, width: '15%' },
    { key: 'start_balance', label: 'الرصيد الافتتاحي', sortable: true, width: '15%' },
    { key: 'credit', label: 'الحد الائتماني', sortable: true, width: '15%' },
    { key: 'actions', label: 'الخيارات', sortable: false, width: '15%' }
];
</script>

<template>
  <TopNavbarLayout title="إدارة العملاء">
    <template #header>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">إدارة العملاء</h1>
                <p class="text-sm text-gray-500 mt-1">إضافة وتعديل بيانات العملاء والحدود الائتمانية</p>
            </div>
            <KodyButton v-if="can('create_customer')" @click="openModal()" variant="primary">
                + عميل جديد
            </KodyButton>
        </div>
    </template>

    <KodyCard noPadding>
        <KodyDataGrid 
            :columns="tableColumns" 
            :data="customers.data"
        >
            <!-- Custom formatting for phone -->
            <template #cell-phone="{ value }">
                <span dir="ltr">{{ value || '-' }}</span>
            </template>

            <!-- Custom formatting for balance -->
            <template #cell-start_balance="{ row }">
                <span :class="row.start_balance < 0 ? 'text-red-600' : 'text-emerald-600'" class="font-medium font-mono">
                    {{ Math.abs(row.start_balance).toLocaleString() }}
                    <span class="text-xs text-gray-400 font-sans ml-1">{{ row.start_balance < 0 ? 'دائن' : 'مدين' }}</span>
                </span>
            </template>

            <!-- Custom formatting for credit limit -->
            <template #cell-credit="{ value }">
                <span v-if="value > 0" class="font-mono text-gray-700">{{ value.toLocaleString() }}</span>
                <span v-else class="text-gray-400 text-xs">بدون حد</span>
            </template>

            <!-- Custom formatting for actions -->
            <template #cell-actions="{ row }">
                <div class="flex items-center justify-start gap-2">
                    <button v-if="can('update_customer')" @click="openModal(row)" class="text-[#017E84] hover:bg-[#017E84]/10 p-1.5 rounded transition" title="تعديل">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <button v-if="can('delete_customer')" @click="deleteCustomer(row.id)" class="text-red-600 hover:bg-red-50 p-1.5 rounded transition" title="حذف">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </template>
        </KodyDataGrid>
    </KodyCard>

    <!-- Create/Edit Modal using KodyModal -->
    <KodyModal :show="isModalOpen" @close="closeModal" maxWidth="xl" :title="form.id ? 'تعديل بيانات العميل' : 'إضافة عميل جديد'">
        <form @submit.prevent="submit" class="space-y-4">
            <KodyInput v-model="form.aname" label="اسم العميل" required :error="form.errors.aname" autofocus />
            
            <div class="grid grid-cols-2 gap-4">
                <KodyInput v-model="form.phone" label="رقم الجوال" :error="form.errors.phone" dir="ltr" />
                <KodyInput v-model="form.info" label="الرقم الضريبي" :error="form.errors.info" dir="ltr" />
            </div>

            <KodyInput v-model="form.address" label="العنوان" :error="form.errors.address" />

            <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4 mt-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الرصيد الافتتاحي</label>
                    <div class="flex">
                        <input v-model="form.start_balance" type="number" step="0.01" class="w-full rounded-r-md shadow-sm border-gray-300 focus:border-[#017E84] focus:ring-[#017E84] sm:text-sm border-l-0" dir="ltr">
                        <div class="w-32">
                            <KodySearchableSelect v-model="form.nature" :options="natureOptions" class="!mb-0" />
                        </div>
                    </div>
                </div>
                <KodyInput v-model="form.credit" type="number" label="الحد الائتماني (أقصى مديونية)" :error="form.errors.credit" dir="ltr" min="0" step="100" />
            </div>
        </form>

        <template #footer>
            <KodyButton @click="submit" :loading="form.processing" variant="primary" class="w-full sm:w-auto sm:mr-3">
                حفظ البيانات
            </KodyButton>
            <KodyButton @click="closeModal" variant="secondary" class="mt-3 w-full sm:mt-0 sm:w-auto">
                إلغاء
            </KodyButton>
        </template>
    </KodyModal>
  </TopNavbarLayout>
</template>
