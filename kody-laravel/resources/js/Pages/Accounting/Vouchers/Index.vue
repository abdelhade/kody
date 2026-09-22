<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyButton from '@/Components/KodyButton.vue';
import KodyModal from '@/Components/KodyModal.vue';
import KodyInput from '@/Components/KodyInput.vue';
import KodyDataGrid from '@/Components/KodyDataGrid.vue';
import KodySearchableSelect from '@/Components/KodySearchableSelect.vue';
import axios from 'axios';

const props = defineProps({
    vouchers: Object,
    type: [Number, String] // 1 = Receipt, 2 = Payment
});

const isReceipt = props.type == 1;

const showCreateModal = ref(false);
const accountsList = ref([]);
const fundsList = ref([]);

// Search accounts
const searchAccounts = async (query) => {
    if (query.length < 2) return;
    const res = await axios.get(route('api.accounts.search'), { params: { query } });
    accountsList.value = res.data.map(a => ({ id: a.id, label: `${a.code} - ${a.aname}` }));
};

const loadFunds = async () => {
    const res = await axios.get(route('api.accounts.search'), { params: { folder: 2 } }); // Assuming 2 is treasury/banks
    fundsList.value = res.data.map(a => ({ id: a.id, label: `${a.code} - ${a.aname}` }));
};

const form = useForm({
    tybe: props.type,
    val: '',
    account: '',
    fund_account: '',
    vdate: new Date().toISOString().split('T')[0],
    info: ''
});

const saveVoucher = () => {
    form.post(route('vouchers.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        }
    });
};

const openCreate = () => {
    loadFunds();
    showCreateModal.value = true;
};

const switchType = (newType) => {
    router.get(route('vouchers.index'), { type: newType });
};

const columns = [
    { field: 'voucher_id', label: 'رقم السند' },
    { field: 'vdate', label: 'التاريخ' },
    { field: 'account_name', label: 'الحساب' },
    { field: 'fund_account_name', label: 'الصندوق/البنك' },
    { field: 'val', label: 'المبلغ' },
    { field: 'info', label: 'البيان' },
];
</script>

<template>
    <Head :title="isReceipt ? 'سندات القبض' : 'سندات الصرف'" />

    <TopNavbarLayout>
        <div class="max-w-7xl mx-auto p-6 space-y-6">
            <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-black text-slate-800">
                        {{ isReceipt ? 'سندات القبض' : 'سندات الصرف' }}
                    </h2>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button @click="switchType(1)" :class="{'bg-white shadow-sm font-bold text-[#017E84]': isReceipt, 'text-gray-500 hover:text-gray-700': !isReceipt}" class="px-4 py-2 rounded-md text-sm transition-all">القبض</button>
                        <button @click="switchType(2)" :class="{'bg-white shadow-sm font-bold text-[#017E84]': !isReceipt, 'text-gray-500 hover:text-gray-700': isReceipt}" class="px-4 py-2 rounded-md text-sm transition-all">الصرف</button>
                    </div>
                </div>
                <KodyButton @click="openCreate" icon="PlusIcon">
                    إضافة سند جديد
                </KodyButton>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <KodyDataGrid
                    :columns="columns"
                    :data="vouchers.data"
                    :pagination="{
                        current_page: vouchers.current_page,
                        last_page: vouchers.last_page,
                        links: vouchers.links
                    }"
                    @page-change="(url) => router.get(url, { type: props.type })"
                >
                </KodyDataGrid>
            </div>
        </div>

        <KodyModal :show="showCreateModal" @close="showCreateModal = false" :title="isReceipt ? 'سند قبض جديد' : 'سند صرف جديد'" size="lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <KodySearchableSelect
                        v-model="form.account"
                        label="الحساب (العميل/المورد/الخ)"
                        :options="accountsList"
                        placeholder="ابحث عن الحساب..."
                        @search="searchAccounts"
                        :error="form.errors.account"
                    />
                </div>
                <div>
                    <KodySearchableSelect
                        v-model="form.fund_account"
                        label="حساب الصندوق / البنك"
                        :options="fundsList"
                        :error="form.errors.fund_account"
                    />
                </div>
                <div>
                    <KodyInput v-model="form.val" type="number" step="0.01" label="المبلغ" :error="form.errors.val" />
                </div>
                <div>
                    <KodyInput v-model="form.vdate" type="date" label="التاريخ" :error="form.errors.vdate" />
                </div>
                <div class="md:col-span-2">
                    <KodyInput v-model="form.info" label="البيان (ملاحظات)" :error="form.errors.info" />
                </div>
            </div>
            
            <template #footer>
                <div class="flex justify-end gap-3 w-full">
                    <KodyButton variant="secondary" @click="showCreateModal = false">إلغاء</KodyButton>
                    <KodyButton @click="saveVoucher" :loading="form.processing">حفظ السند</KodyButton>
                </div>
            </template>
        </KodyModal>
    </TopNavbarLayout>
</template>
