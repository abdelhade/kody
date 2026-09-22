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
  groups: Object
});

const page = usePage();
const can = (permission) => page.props.auth?.permissions?.includes(permission) || true;

const isModalOpen = ref(false);

const form = useForm({
  id: null,
  gname: '',
  group_under: null,
  info: '',
});

const openModal = (group = null) => {
  if (group) {
    form.id = group.id;
    form.gname = group.gname;
    form.group_under = group.group_under;
    form.info = group.info || '';
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
    form.put(`/master-data/item-groups/${form.id}`, {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post('/master-data/item-groups', {
      onSuccess: () => closeModal(),
    });
  }
};

const deleteGroup = (id) => {
  if (confirm('هل أنت متأكد من حذف هذه المجموعة؟ تأكد من عدم وجود أصناف تحتها.')) {
    form.delete(`/master-data/item-groups/${id}`);
  }
};

const tableColumns = [
    { key: 'id', label: 'م', sortable: true, width: '10%' },
    { key: 'gname', label: 'اسم المجموعة', sortable: true, width: '30%' },
    { key: 'parent_group', label: 'تتفرع من', sortable: true, width: '25%' },
    { key: 'info', label: 'ملاحظات', sortable: false, width: '20%' },
    { key: 'actions', label: 'الخيارات', sortable: false, width: '15%' }
];
</script>

<template>
  <TopNavbarLayout title="مجموعات الأصناف">
    <template #header>
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">مجموعات الأصناف</h1>
                <p class="text-sm text-gray-500 mt-1">إدارة الأقسام والتصنيفات للمنتجات</p>
            </div>
            <KodyButton v-if="can('create_item_group')" @click="openModal()" variant="primary">
                + مجموعة جديدة
            </KodyButton>
        </div>
    </template>

    <KodyCard noPadding>
        <KodyDataGrid 
            :columns="tableColumns" 
            :data="groups.data"
        >
            <template #cell-parent_group="{ row }">
                <span v-if="row.parent_group" class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                    {{ row.parent_group.gname }}
                </span>
                <span v-else class="text-gray-400">-</span>
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center justify-start gap-2">
                    <button v-if="can('update_item_group')" @click="openModal(row)" class="text-[#017E84] hover:bg-[#017E84]/10 p-1.5 rounded transition" title="تعديل">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <button v-if="can('delete_item_group')" @click="deleteGroup(row.id)" class="text-red-600 hover:bg-red-50 p-1.5 rounded transition" title="حذف">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </template>
        </KodyDataGrid>
    </KodyCard>

    <KodyModal :show="isModalOpen" @close="closeModal" maxWidth="md" :title="form.id ? 'تعديل بيانات المجموعة' : 'إضافة مجموعة جديدة'">
        <form @submit.prevent="submit" class="space-y-4">
            <KodyInput v-model="form.gname" label="اسم المجموعة" required :error="form.errors.gname" autofocus />
            
            <KodySearchableSelect 
                v-model="form.group_under" 
                :options="groups.data" 
                displayKey="gname" 
                valueKey="id" 
                label="تتفرع من (المجموعة الأب)" 
                placeholder="بدون (مجموعة رئيسية)"
                :error="form.errors.group_under" 
            />

            <KodyInput v-model="form.info" label="ملاحظات إضافية" :error="form.errors.info" />
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
