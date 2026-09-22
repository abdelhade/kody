<template>
  <TopNavbarLayout>
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">
          <Link href="/master-data/items" class="text-blue-600 hover:underline mr-2">← رجوع</Link>
          {{ isEditing ? 'تعديل الصنف' : 'إضافة صنف جديد' }}
        </h1>
      </div>
      <button @click="submit" :disabled="form.processing" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
        <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        حفظ بيانات الصنف
      </button>
    </div>

    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Basic Info Column -->
      <div class="md:col-span-1 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
          <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">البيانات الأساسية</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">اسم الصنف <span class="text-red-500">*</span></label>
              <input v-model="form.iname" type="text" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
              <div v-if="form.errors.iname" class="text-red-500 text-xs mt-1">{{ form.errors.iname }}</div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">المجموعة (Category) <span class="text-red-500">*</span></label>
              <select v-model="form.group1" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                <option value="" disabled>اختر المجموعة...</option>
                <option v-for="group in groups" :key="group.id" :value="group.id">
                  {{ group.gname }}
                </option>
              </select>
              <div v-if="form.errors.group1" class="text-red-500 text-xs mt-1">{{ form.errors.group1 }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">التسعير الأساسي للمخزون</label>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs text-slate-500 mb-1">سعر التكلفة الافتراضي</label>
                  <input v-model="form.cost_price" type="number" step="0.01" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" dir="ltr">
                </div>
                <div>
                  <label class="block text-xs text-slate-500 mb-1">سعر البيع (للوحدة الأساسية)</label>
                  <input v-model="form.price1" type="number" step="0.01" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" dir="ltr">
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">معلومات إضافية (اختياري)</label>
              <textarea v-model="form.info" rows="2" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Multiple Units & Barcodes Column -->
      <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
          <div class="flex justify-between items-center border-b border-slate-100 pb-2 mb-4">
            <h3 class="text-sm font-bold text-slate-800">وحدات القياس والباركود (Multiple UoM & Barcodes)</h3>
            <button type="button" @click="addUnit" class="text-blue-600 text-xs font-semibold hover:text-blue-800 flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
              إضافة وحدة أخرى (مثل: كرتونة)
            </button>
          </div>
          
          <div v-if="form.errors.units" class="text-red-500 text-xs mb-3">{{ form.errors.units }}</div>

          <div class="space-y-4">
            <div v-for="(unit, index) in form.units" :key="index" class="p-4 bg-slate-50 border border-slate-200 rounded-lg relative">
              
              <!-- Remove Unit Button -->
              <button v-if="index > 0" type="button" @click="removeUnit(index)" class="absolute top-3 left-3 text-red-400 hover:text-red-600" title="إزالة هذه الوحدة">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                  <label class="block text-xs font-bold text-slate-700 mb-1">
                    {{ index === 0 ? 'الوحدة الأساسية (الصغرى)' : 'اسم الوحدة الكبرى' }}
                  </label>
                  <input v-model="unit.unit_name" type="text" class="w-full rounded border-slate-300 text-sm py-1.5" :placeholder="index === 0 ? 'مثال: حبة، قطعة' : 'مثال: كرتونة، دستة'" required>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 mb-1">
                    {{ index === 0 ? 'معامل التحويل' : 'تحتوي على كم وحدة صغرى؟' }}
                  </label>
                  <input v-model="unit.u_val" type="number" min="1" step="0.01" class="w-full rounded border-slate-300 text-sm py-1.5 bg-white" dir="ltr" :readonly="index === 0" required>
                  <p v-if="index > 0 && unit.u_val > 1" class="text-[10px] text-emerald-600 mt-1">الـ {{ unit.unit_name || 'وحدة' }} تساوي {{ unit.u_val }} من الوحدة الأساسية.</p>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 mb-1">سعر بيع الـ {{ unit.unit_name || 'وحدة' }}</label>
                  <input v-model="unit.price1" type="number" step="0.01" class="w-full rounded border-slate-300 text-sm py-1.5 text-blue-700 font-bold" dir="ltr" required>
                </div>
              </div>

              <!-- Barcodes for this unit -->
              <div class="mt-3 pt-3 border-t border-slate-200">
                <label class="block text-xs font-bold text-slate-700 mb-2">أرقام الباركود المرتبطة بهذه الوحدة</label>
                <div class="flex flex-wrap gap-2">
                  <div v-for="(barcode, bIndex) in unit.barcodes" :key="bIndex" class="flex items-center bg-white border border-slate-300 rounded px-2 py-1 shadow-sm">
                    <span class="text-sm font-mono text-slate-700 mr-2" dir="ltr">{{ barcode }}</span>
                    <button type="button" @click="removeBarcode(index, bIndex)" class="text-slate-400 hover:text-red-500">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                  </div>
                  
                  <div class="flex items-center w-48 relative">
                    <input type="text" v-model="unit.newBarcode" @keydown.enter.prevent="addBarcode(index)" placeholder="امسح الباركود هنا..." class="w-full rounded border-slate-300 text-xs py-1.5 pl-8 focus:ring-1 focus:ring-blue-500" dir="ltr">
                    <button type="button" @click="addBarcode(index)" class="absolute left-1 top-1.5 text-emerald-600 hover:text-emerald-800 p-0.5">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                  </div>
                </div>
              </div>

            </div>
          </div>
          
        </div>
      </div>
      
    </form>
  </TopNavbarLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';

const props = defineProps({
  item: { type: Object, default: () => ({}) },
  groups: { type: Array, default: () => [] }
});

const isEditing = computed(() => !!props.item);

// Transform item units to form format if editing
const initialUnits = isEditing.value && props.item.units ? props.item.units.map(u => ({
  unit_name: u.unit_name || 'حبة',
  u_val: u.u_val,
  price1: u.price1,
  barcodes: u.barcodes ? u.barcodes.map(b => b.barcode) : [],
  newBarcode: ''
})) : [
  { unit_name: 'حبة', u_val: 1, price1: 0, barcodes: [], newBarcode: '' } // Base Unit
];

const form = useForm({
  iname: props.item?.iname || '',
  group1: props.item?.group1 || '',
  info: props.item?.info || '',
  cost_price: props.item?.cost_price || 0,
  price1: props.item?.price1 || 0,
  units: initialUnits
});

const addUnit = () => {
  form.units.push({
    unit_name: '',
    u_val: 1,
    price1: 0,
    barcodes: [],
    newBarcode: ''
  });
};

const removeUnit = (index) => {
  form.units.splice(index, 1);
};

const addBarcode = (unitIndex) => {
  const code = form.units[unitIndex].newBarcode.trim();
  if (code && !form.units[unitIndex].barcodes.includes(code)) {
    form.units[unitIndex].barcodes.push(code);
    form.units[unitIndex].newBarcode = '';
  }
};

const removeBarcode = (unitIndex, barcodeIndex) => {
  form.units[unitIndex].barcodes.splice(barcodeIndex, 1);
};

const submit = () => {
  // Sync the base price1 with the first unit's price1 just in case
  if (form.units.length > 0) {
    form.price1 = form.units[0].price1;
  }

  if (isEditing.value) {
    form.put(`/master-data/items/${props.item.id}`);
  } else {
    form.post('/master-data/items');
  }
};
</script>
