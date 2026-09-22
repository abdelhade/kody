<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-800">شجرة الحسابات والمالية</h2>
          <p class="text-xs text-slate-500 mt-1">إدارة دليل الحسابات الموحد، العملاء، الموردين، الخزائن النقدية، والبنوك.</p>
        </div>
        <div>
          <button
            @click="showAddModal = true"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-500/20 transition-all cursor-pointer"
          >
            <span>+ إضافة حساب / طرف جديد</span>
          </button>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400">إجمالي العملاء</span>
            <p class="text-xl font-black text-slate-800 mt-0.5">{{ stats.total_clients }}</p>
          </div>
          <span class="text-2xl">👥</span>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400">إجمالي الموردين</span>
            <p class="text-xl font-black text-purple-600 mt-0.5">{{ stats.total_suppliers }}</p>
          </div>
          <span class="text-2xl">🚚</span>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400">رصيد الخزائن النقدية</span>
            <p class="text-xl font-black text-emerald-600 mt-0.5 font-mono">
              {{ Number(stats.total_funds_cash).toLocaleString('ar-EG') }} ج.م
            </p>
          </div>
          <span class="text-2xl">💵</span>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400">أرصدة البنوك</span>
            <p class="text-xl font-black text-blue-600 mt-0.5 font-mono">
              {{ Number(stats.total_banks_cash).toLocaleString('ar-EG') }} ج.م
            </p>
          </div>
          <span class="text-2xl">🏦</span>
        </div>
      </div>

      <!-- Tab Buttons & Search -->
      <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="flex gap-1.5 overflow-x-auto w-full sm:w-auto">
          <button
            v-for="t in [
              { key: 'tree', label: '🌳 دليل الحسابات الشجري' },
              { key: 'clients', label: '👥 العملاء' },
              { key: 'suppliers', label: '🚚 الموردين' },
              { key: 'funds', label: '💰 الخزائن والبنوك' }
            ]"
            :key="t.key"
            @click="switchTab(t.key)"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer whitespace-nowrap',
              activeTab === t.key ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'
            ]"
          >
            {{ t.label }}
          </button>
        </div>

        <div v-if="activeTab !== 'tree'" class="w-full sm:w-72 relative">
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            placeholder="بحث بالاسم أو الكود أو الهاتف..."
            class="w-full pr-9 pl-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <span class="absolute right-3 top-2 text-xs text-slate-400">🔍</span>
        </div>
      </div>

      <!-- Tab 1: Tree View -->
      <div v-if="activeTab === 'tree'" class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-6 space-y-4">
        <h3 class="font-black text-slate-800 text-base border-b border-slate-100 pb-3">شجرة الحسابات الهيكلية</h3>
        <div class="space-y-3 font-mono text-sm">
          <div
            v-for="root in treeAccounts"
            :key="root.id"
            class="border border-slate-200 rounded-xl p-3 bg-slate-50/50"
          >
            <div class="flex items-center justify-between font-bold text-slate-800">
              <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span class="text-blue-700">[{{ root.code }}]</span>
                <span>{{ root.aname }}</span>
              </span>
              <span class="text-xs text-slate-500 font-normal">
                الرصيد: {{ Number(root.balance || 0).toLocaleString('ar-EG') }} ج.م
              </span>
            </div>

            <!-- Level 2 Children -->
            <div v-if="root.children && root.children.length > 0" class="mr-6 mt-2 space-y-2 border-r-2 border-slate-200 pr-3">
              <div v-for="child in root.children" :key="child.id" class="text-xs">
                <div class="flex items-center justify-between font-semibold text-slate-700">
                  <span class="flex items-center gap-1.5">
                    <span class="text-slate-400">↳</span>
                    <span class="text-indigo-600">[{{ child.code }}]</span>
                    <span>{{ child.aname }}</span>
                  </span>
                  <span class="text-slate-500 font-mono">{{ Number(child.balance || 0).toLocaleString('ar-EG') }}</span>
                </div>

                <!-- Level 3 Children -->
                <div v-if="child.children && child.children.length > 0" class="mr-5 mt-1.5 space-y-1 border-r border-slate-200 pr-2">
                  <div
                    v-for="sub in child.children"
                    :key="sub.id"
                    class="flex items-center justify-between text-[11px] text-slate-600 hover:bg-slate-100/60 p-1 rounded transition-colors"
                  >
                    <span>
                      <span class="text-slate-400">--</span>
                      <span class="text-emerald-700 font-bold ml-1">[{{ sub.code }}]</span>
                      <span>{{ sub.aname }}</span>
                    </span>
                    <span class="font-mono text-slate-500">{{ Number(sub.balance || 0).toLocaleString('ar-EG') }} ج.م</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab 2: Clients -->
      <div v-if="activeTab === 'clients'" class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <table class="w-full text-right text-sm">
          <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500">
            <tr>
              <th class="py-3 px-4">كود العميل</th>
              <th class="py-3 px-4">اسم العميل</th>
              <th class="py-3 px-4">الهاتف</th>
              <th class="py-3 px-4">العنوان</th>
              <th class="py-3 px-4 text-left">الرصيد الحالي</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            <tr v-for="c in clients.data" :key="c.id" class="hover:bg-slate-50/50">
              <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ c.code }}</td>
              <td class="py-3 px-4 font-bold text-slate-800">{{ c.aname }}</td>
              <td class="py-3 px-4 text-slate-500 font-mono">{{ c.phone || '—' }}</td>
              <td class="py-3 px-4 text-slate-500">{{ c.address || '—' }}</td>
              <td class="py-3 px-4 text-left font-mono font-bold" :class="c.balance < 0 ? 'text-red-600' : 'text-emerald-600'">
                {{ Number(c.balance || 0).toLocaleString('ar-EG') }} ج.م
              </td>
            </tr>
            <tr v-if="!clients.data || clients.data.length === 0">
              <td colspan="5" class="py-8 text-center text-slate-400">لا يوجد عملاء مسجلين</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tab 3: Suppliers -->
      <div v-if="activeTab === 'suppliers'" class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
        <table class="w-full text-right text-sm">
          <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500">
            <tr>
              <th class="py-3 px-4">كود المورد</th>
              <th class="py-3 px-4">اسم المورد</th>
              <th class="py-3 px-4">الهاتف</th>
              <th class="py-3 px-4 text-left">الرصيد المستحق</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            <tr v-for="s in suppliers.data" :key="s.id" class="hover:bg-slate-50/50">
              <td class="py-3 px-4 font-mono font-bold text-purple-600">{{ s.code }}</td>
              <td class="py-3 px-4 font-bold text-slate-800">{{ s.aname }}</td>
              <td class="py-3 px-4 text-slate-500 font-mono">{{ s.phone || '—' }}</td>
              <td class="py-3 px-4 text-left font-mono font-bold text-slate-800">
                {{ Number(s.balance || 0).toLocaleString('ar-EG') }} ج.م
              </td>
            </tr>
            <tr v-if="!suppliers.data || suppliers.data.length === 0">
              <td colspan="4" class="py-8 text-center text-slate-400">لا يوجد موردين مسجلين</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tab 4: Funds & Banks -->
      <div v-if="activeTab === 'funds'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cash Funds -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">الخزائن والصناديق النقدية</h3>
            <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded font-mono font-bold">121</span>
          </div>
          <div class="divide-y divide-slate-100">
            <div v-for="f in funds" :key="f.id" class="p-3.5 flex items-center justify-between text-xs">
              <div>
                <span class="font-bold text-slate-800 block">{{ f.aname }}</span>
                <span class="text-[11px] font-mono text-slate-400">{{ f.code }}</span>
              </div>
              <span class="font-mono font-black text-sm text-emerald-600">
                {{ Number(f.balance || 0).toLocaleString('ar-EG') }} ج.م
              </span>
            </div>
          </div>
        </div>

        <!-- Banks -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">الحسابات البنكية</h3>
            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-mono font-bold">124</span>
          </div>
          <div class="divide-y divide-slate-100">
            <div v-for="b in banks" :key="b.id" class="p-3.5 flex items-center justify-between text-xs">
              <div>
                <span class="font-bold text-slate-800 block">{{ b.aname }}</span>
                <span class="text-[11px] font-mono text-slate-400">{{ b.code }}</span>
              </div>
              <span class="font-mono font-black text-sm text-blue-600">
                {{ Number(b.balance || 0).toLocaleString('ar-EG') }} ج.م
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Account Modal -->
      <div
        v-if="showAddModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
      >
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" dir="rtl">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-black text-slate-800">إضافة حساب / طرف جديد</h3>
            <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
          </div>

          <form @submit.prevent="submitCreateAccount" class="space-y-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">الحساب الرئيسي التابع له *</label>
              <select
                v-model="form.parent_id"
                required
                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
              >
                <option v-for="p in parentAccounts" :key="p.id" :value="p.id">
                  [{{ p.code }}] {{ p.aname }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">اسم الحساب / العميل / المورد *</label>
              <input
                v-model="form.aname"
                type="text"
                required
                class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="مثال: شركة الأمل للتجارة / عميل نقدي"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">كود الحساب (اختياري)</label>
                <input
                  v-model="form.code"
                  type="text"
                  class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg font-mono focus:outline-none"
                  placeholder="توليد تلقائي"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رصيد أول المدة</label>
                <input
                  v-model.number="form.start_balance"
                  type="number"
                  step="any"
                  class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg font-mono focus:outline-none"
                  placeholder="0"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الهاتف</label>
                <input
                  v-model="form.phone"
                  type="text"
                  class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg font-mono focus:outline-none"
                  placeholder="01xxxxxxxxx"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">العنوان</label>
                <input
                  v-model="form.address"
                  type="text"
                  class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none"
                  placeholder="القاهرة، مصر"
                />
              </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
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
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-md transition-all cursor-pointer disabled:opacity-50"
              >
                {{ isSubmitting ? 'جاري الحفظ...' : 'حفظ الحساب' }}
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
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  tab: String,
  clients: Object,
  suppliers: Object,
  funds: Array,
  banks: Array,
  treeAccounts: Array,
  stats: Object,
  parentAccounts: Array,
  filters: Object,
});

const activeTab = ref(props.tab || 'tree');
const searchQuery = ref(props.filters.search || '');
const showAddModal = ref(false);
const isSubmitting = ref(false);

const form = ref({
  aname: '',
  code: '',
  parent_id: props.parentAccounts[0]?.id || 19,
  phone: '',
  address: '',
  start_balance: 0,
});

function switchTab(t) {
  activeTab.value = t;
  router.get('/accounts', { tab: t }, { preserveState: true, replace: true });
}

let searchTimer = null;
function handleSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    router.get('/accounts', { tab: activeTab.value, search: searchQuery.value }, { preserveState: true, replace: true });
  }, 300);
}

function submitCreateAccount() {
  isSubmitting.value = true;
  router.post('/accounts', form.value, {
    onSuccess: () => {
      showAddModal.value = false;
      isSubmitting.value = false;
      form.value = {
        aname: '',
        code: '',
        parent_id: props.parentAccounts[0]?.id || 19,
        phone: '',
        address: '',
        start_balance: 0,
      };
    },
    onError: () => {
      isSubmitting.value = false;
    },
  });
}
</script>
