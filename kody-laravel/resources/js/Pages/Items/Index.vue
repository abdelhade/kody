<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header & Stats -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-800">إدارة الأصناف والأسعار</h2>
          <p class="text-xs text-slate-500 mt-1">عرض وتعديل الأصناف، الوحدات المتعددة، والباركودات الخاصة بالمخزن.</p>
        </div>
        <div>
          <button
            @click="openAddModal"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-500/20 transition-all cursor-pointer"
          >
            <span>+ إضافة صنف جديد</span>
          </button>
        </div>
      </div>

      <!-- Quick Metrics -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400">إجمالي الأصناف</span>
            <p class="text-xl font-black text-slate-800 mt-0.5">{{ stats.total_items }}</p>
          </div>
          <span class="text-2xl">📦</span>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400">إجمالي كمية المخزون</span>
            <p class="text-xl font-black text-emerald-600 mt-0.5">{{ stats.total_stock }}</p>
          </div>
          <span class="text-2xl">📊</span>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400">عدد مجموعات الأصناف</span>
            <p class="text-xl font-black text-indigo-600 mt-0.5">{{ stats.groups_count }}</p>
          </div>
          <span class="text-2xl">🗂️</span>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="w-full sm:w-80 relative">
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            placeholder="بحث بالاسم أو الباركود أو الكود..."
            class="w-full pr-10 pl-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
          />
          <span class="absolute right-3 top-2.5 text-slate-400">🔍</span>
        </div>

        <div class="w-full sm:w-auto flex items-center gap-3">
          <select
            v-model="selectedGroup"
            @change="handleGroupFilter"
            class="px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
          >
            <option value="">جميع المجموعات</option>
            <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.gname }}</option>
          </select>
        </div>
      </div>

      <!-- Items Table -->
      <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm">
            <thead class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase">
              <tr>
                <th class="py-3.5 px-4">#</th>
                <th class="py-3.5 px-4">اسم الصنف</th>
                <th class="py-3.5 px-4">الباركود الرئيسي</th>
                <th class="py-3.5 px-4">المجموعة</th>
                <th class="py-3.5 px-4">الوحدات والأسعار</th>
                <th class="py-3.5 px-4">رصيد المخزن</th>
                <th class="py-3.5 px-4 text-center">إجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(item, idx) in items.data" :key="item.id" class="hover:bg-slate-50/50 transition-colors">
                <td class="py-3.5 px-4 text-slate-400 font-mono">{{ idx + 1 }}</td>
                <td class="py-3.5 px-4 font-bold text-slate-800">
                  {{ item.iname }}
                  <span v-if="item.name2" class="block text-xs text-slate-400 font-normal">{{ item.name2 }}</span>
                </td>
                <td class="py-3.5 px-4 font-mono text-xs text-slate-600 bg-slate-50/40 inline-block my-2 rounded px-2 py-1">
                  {{ item.barcode || '—' }}
                </td>
                <td class="py-3.5 px-4 text-slate-600">
                  <span class="inline-block px-2.5 py-1 bg-slate-100 rounded-md text-xs font-medium">
                    {{ item.group?.gname || 'بدون مجموعة' }}
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex flex-wrap gap-1.5 max-w-md">
                    <span
                      v-for="u in item.units"
                      :key="u.id"
                      class="inline-flex items-center gap-1 text-xs px-2 py-0.5 bg-blue-50 border border-blue-100 text-blue-700 rounded-md"
                    >
                      <strong class="font-bold">{{ u.unit?.uname || 'وحدة' }}:</strong>
                      <span class="font-black">{{ Number(u.price1).toLocaleString('ar-EG') }} ج.م</span>
                    </span>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <span
                    :class="[
                      'inline-block px-2.5 py-0.5 rounded-full text-xs font-bold font-mono',
                      item.itmqty > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'
                    ]"
                  >
                    {{ item.itmqty || 0 }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <button
                    @click="deleteItem(item)"
                    class="text-xs text-red-600 hover:text-red-800 font-bold px-2 py-1 rounded hover:bg-red-50 cursor-pointer"
                  >
                    حذف
                  </button>
                </td>
              </tr>
              <tr v-if="!items.data || items.data.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                  لا توجد أصناف مطابقة للبحث
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="items.links && items.links.length > 3" class="p-4 border-t border-slate-100 flex items-center justify-between">
          <span class="text-xs text-slate-500">
            عرض {{ items.from }} إلى {{ items.to }} من إجمالي {{ items.total }} صنف
          </span>
          <div class="flex gap-1">
            <Link
              v-for="(link, lIdx) in items.links"
              :key="lIdx"
              :href="link.url || '#'"
              :class="[
                'px-3 py-1 rounded text-xs font-semibold',
                link.active ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200',
                !link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'
              ]"
              v-html="link.label"
            />
          </div>
        </div>
      </div>

      <!-- Add Item Modal -->
      <div
        v-if="showAddModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
      >
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-5 my-8 max-h-[90vh] overflow-y-auto" dir="rtl">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-lg font-black text-slate-800">إضافة صنف جديد</h3>
            <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
          </div>

          <form @submit.prevent="submitCreateItem" class="space-y-4">
            <!-- Row 1: Name and Secondary Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">اسم الصنف (عربي) *</label>
                <input
                  v-model="form.iname"
                  type="text"
                  required
                  class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  placeholder="مثال: شاي العروسة 250جم"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الاسم الإنجليزي / البديل</label>
                <input
                  v-model="form.name2"
                  type="text"
                  class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                  placeholder="Tea El-Arosa"
                />
              </div>
            </div>

            <!-- Row 2: Barcode, Code, Group -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الباركود الرئيسي</label>
                <input
                  v-model="form.barcode"
                  type="text"
                  class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono"
                  placeholder="اتركه فارغاً للتوليد التلقائي"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">كود الصنف (الداخلي)</label>
                <input
                  v-model="form.code"
                  type="text"
                  class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono"
                  placeholder="101"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">مجموعة الصنف</label>
                <select
                  v-model="form.group1"
                  class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer"
                >
                  <option :value="0">بدون مجموعة</option>
                  <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.gname }}</option>
                </select>
              </div>
            </div>

            <!-- Units Repeater Header -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
              <div>
                <h4 class="text-sm font-bold text-slate-800">وحدات الصنف والأسعار *</h4>
                <p class="text-xs text-slate-400">يمكنك تعريف أكثر من وحدة لنفس الصنف (قطعة، دستة، كرتونة...).</p>
              </div>
              <button
                type="button"
                @click="addUnitRow"
                class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors cursor-pointer"
              >
                + إضافة وحدة أخرى
              </button>
            </div>

            <!-- Units Rows -->
            <div class="space-y-3">
              <div
                v-for="(u, uIdx) in form.units"
                :key="uIdx"
                class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2 relative"
              >
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-blue-700">الوحدة رقم {{ uIdx + 1 }}</span>
                  <button
                    v-if="form.units.length > 1"
                    type="button"
                    @click="removeUnitRow(uIdx)"
                    class="text-xs text-red-500 hover:text-red-700 font-bold cursor-pointer"
                  >
                    إزالة
                  </button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                  <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-0.5">الوحدة</label>
                    <select
                      v-model="u.unit_id"
                      required
                      class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-md focus:outline-none"
                    >
                      <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.uname }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-0.5">معامل التحويل (u_val)</label>
                    <input
                      v-model.number="u.u_val"
                      type="number"
                      step="any"
                      min="0.001"
                      required
                      class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-md focus:outline-none font-mono"
                    />
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-0.5">سعر البيع 1 (قطاعي) *</label>
                    <input
                      v-model.number="u.price1"
                      type="number"
                      step="any"
                      min="0"
                      required
                      class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-md focus:outline-none font-mono font-bold text-blue-600"
                    />
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-0.5">سعر التكلفة</label>
                    <input
                      v-model.number="u.cost_price"
                      type="number"
                      step="any"
                      min="0"
                      class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-md focus:outline-none font-mono"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                  <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-0.5">باركود الوحدة (اختياري)</label>
                    <input
                      v-model="u.unit_barcode"
                      type="text"
                      placeholder="اتركه فارغاً للتوليد التلقائي"
                      class="w-full px-2 py-1 text-xs bg-white border border-slate-200 rounded-md font-mono"
                    />
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-0.5">سعر الجملة (سعر 2)</label>
                    <input
                      v-model.number="u.price2"
                      type="number"
                      step="any"
                      min="0"
                      class="w-full px-2 py-1 text-xs bg-white border border-slate-200 rounded-md font-mono"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                type="button"
                @click="showAddModal = false"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg cursor-pointer"
              >
                إلغاء
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-md transition-all cursor-pointer disabled:opacity-50"
              >
                {{ isSubmitting ? 'جاري الحفظ...' : 'حفظ الصنف' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  items: Object,
  groups: Array,
  units: Array,
  filters: Object,
  stats: Object,
});

const searchQuery = ref(props.filters.search || '');
const selectedGroup = ref(props.filters.group || '');
const showAddModal = ref(false);
const isSubmitting = ref(false);

const form = ref({
  iname: '',
  name2: '',
  code: '',
  barcode: '',
  group1: 0,
  info: '',
  units: [],
});

function openAddModal() {
  form.value = {
    iname: '',
    name2: '',
    code: '',
    barcode: '',
    group1: 0,
    info: '',
    units: [
      {
        unit_id: props.units[0]?.id || 1,
        u_val: 1,
        price1: 0,
        price2: 0,
        price3: 0,
        cost_price: 0,
        unit_barcode: '',
      },
    ],
  };
  showAddModal.value = true;
}

function addUnitRow() {
  form.value.units.push({
    unit_id: props.units[0]?.id || 1,
    u_val: 1,
    price1: 0,
    price2: 0,
    price3: 0,
    cost_price: 0,
    unit_barcode: '',
  });
}

function removeUnitRow(index) {
  if (form.value.units.length > 1) {
    form.value.units.splice(index, 1);
  }
}

let searchTimer = null;
function handleSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    router.get('/items', { search: searchQuery.value, group: selectedGroup.value }, { preserveState: true, replace: true });
  }, 300);
}

function handleGroupFilter() {
  router.get('/items', { search: searchQuery.value, group: selectedGroup.value }, { preserveState: true, replace: true });
}

function submitCreateItem() {
  isSubmitting.value = true;
  router.post('/items', form.value, {
    onSuccess: () => {
      showAddModal.value = false;
      isSubmitting.value = false;
    },
    onError: () => {
      isSubmitting.value = false;
    },
  });
}

function deleteItem(item) {
  if (confirm(`هل أنت متأكد من رغبتك في حذف الصنف "${item.iname}"؟`)) {
    router.delete(`/items/${item.id}`);
  }
}
</script>
