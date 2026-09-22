<template>
  <div class="h-screen flex flex-col bg-slate-100 font-sans overflow-hidden select-none" dir="rtl">
    <!-- POS Header -->
    <header class="bg-slate-900 text-white h-14 px-4 flex items-center justify-between shadow-md shrink-0">
      <div class="flex items-center gap-3">
        <Link href="/" class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-black text-sm text-white">
          K
        </Link>
        <div>
          <h1 class="font-black text-sm tracking-tight leading-tight">كودي كاشير — POS</h1>
          <span class="text-[11px] text-slate-400">الفرع: {{ $page.props.currentTenant?.name || 'الرئيسي' }}</span>
        </div>
      </div>

      <!-- Live Clock & Status -->
      <div class="flex items-center gap-4 text-xs font-mono">
        <span class="bg-slate-800 text-emerald-400 px-3 py-1 rounded-full flex items-center gap-1.5 border border-slate-700">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>متصل (Online)</span>
        </span>
        <span class="text-slate-300">{{ currentTime }}</span>
        <Link href="/" class="text-slate-400 hover:text-white text-xs px-2 py-1 rounded hover:bg-slate-800 transition-colors">
          خروج &larr;
        </Link>
      </div>
    </header>

    <!-- POS Main Layout: 2 Columns -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Right Side: Items Catalog & Quick Search (60%) -->
      <div class="flex-1 flex flex-col p-3 overflow-hidden border-l border-slate-200">
        <!-- Barcode Search Bar -->
        <div class="bg-white p-2.5 rounded-xl shadow-xs border border-slate-200/80 mb-3 shrink-0 flex items-center gap-2">
          <span class="text-lg text-slate-400 mr-2">🔍</span>
          <input
            ref="barcodeInput"
            v-model="barcodeScan"
            @keydown.enter="handleBarcodeSubmit"
            type="text"
            placeholder="امسح الباركود أو ابحث باسم الصنف (Enter للإضافة)..."
            class="flex-1 text-sm bg-transparent border-none focus:outline-none font-bold text-slate-800 placeholder:text-slate-400"
            autofocus
          />
          <span class="text-[11px] bg-slate-100 text-slate-500 px-2 py-1 rounded font-mono">F2 للتركيز</span>
        </div>

        <!-- Categories Tabs -->
        <div class="flex gap-1.5 overflow-x-auto pb-2 mb-2 shrink-0 scrollbar-none">
          <button
            @click="activeCategory = 0"
            :class="[
              'px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap cursor-pointer',
              activeCategory === 0 ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200'
            ]"
          >
            جميع الأصناف
          </button>
          <button
            v-for="cat in categories"
            :key="cat.id"
            @click="activeCategory = cat.id"
            :class="[
              'px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap cursor-pointer',
              activeCategory === cat.id ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200'
            ]"
          >
            {{ cat.gname }}
          </button>
        </div>

        <!-- Items Grid -->
        <div class="flex-1 overflow-y-auto pr-1">
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5">
            <div
              v-for="item in filteredItems"
              :key="item.id"
              @click="addToCart(item)"
              class="bg-white rounded-xl p-3 border border-slate-200/80 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between group active:scale-95"
            >
              <div>
                <h4 class="font-bold text-xs text-slate-800 line-clamp-2 group-hover:text-blue-600 transition-colors">
                  {{ item.iname }}
                </h4>
                <span class="text-[10px] text-slate-400 font-mono mt-1 block">{{ item.barcode || '—' }}</span>
              </div>
              <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-100">
                <span class="text-xs font-black text-emerald-600">
                  {{ Number(item.price1 || 0).toLocaleString('ar-EG') }} ج.م
                </span>
                <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold group-hover:bg-blue-600 group-hover:text-white transition-colors">
                  +
                </span>
              </div>
            </div>
          </div>

          <div v-if="filteredItems.length === 0" class="h-48 flex items-center justify-center text-slate-400 text-xs">
            لا توجد أصناف في هذا القسم
          </div>
        </div>
      </div>

      <!-- Left Side: Active Cart & Checkout (40%) -->
      <div class="w-96 lg:w-[420px] bg-white flex flex-col shadow-lg border-r border-slate-200 shrink-0">
        <!-- Cart Header -->
        <div class="p-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
          <div class="flex items-center gap-2">
            <span class="text-base">🛒</span>
            <h3 class="font-black text-sm text-slate-800">فاتورة حالية ({{ cart.length }} أصناف)</h3>
          </div>
          <button
            v-if="cart.length > 0"
            @click="clearCart"
            class="text-[11px] text-red-600 hover:text-red-700 font-bold px-2 py-1 rounded hover:bg-red-50 cursor-pointer"
          >
            تفريغ السلة (F9)
          </button>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1.5 divide-y divide-slate-100">
          <div
            v-for="(row, idx) in cart"
            :key="idx"
            class="pt-2 first:pt-0 flex items-center justify-between gap-2 text-xs"
          >
            <div class="flex-1 min-w-0">
              <h5 class="font-bold text-slate-800 truncate">{{ row.iname }}</h5>
              <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                <span>سعر: {{ Number(row.price).toLocaleString('ar-EG') }}</span>
                <span v-if="row.units && row.units.length > 1" class="text-blue-600 font-semibold">
                  [{{ row.selectedUnitName }}]
                </span>
              </div>
            </div>

            <!-- Qty Controls -->
            <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-0.5 shrink-0">
              <button
                @click="decreaseQty(idx)"
                class="w-6 h-6 rounded bg-white hover:bg-slate-200 flex items-center justify-center font-bold text-slate-700 cursor-pointer"
              >
                -
              </button>
              <span class="w-8 text-center font-black text-xs font-mono">{{ row.qty }}</span>
              <button
                @click="increaseQty(idx)"
                class="w-6 h-6 rounded bg-white hover:bg-slate-200 flex items-center justify-center font-bold text-slate-700 cursor-pointer"
              >
                +
              </button>
            </div>

            <!-- Total for Line -->
            <div class="w-18 text-left font-black text-slate-800 shrink-0 font-mono">
              {{ Number(row.qty * row.price).toLocaleString('ar-EG') }}
            </div>

            <!-- Remove Item -->
            <button
              @click="removeFromCart(idx)"
              class="text-slate-300 hover:text-red-500 font-bold text-sm px-1 cursor-pointer"
            >
              &times;
            </button>
          </div>

          <div v-if="cart.length === 0" class="h-64 flex flex-col items-center justify-center text-slate-400 gap-2">
            <span class="text-3xl">🛍️</span>
            <p class="text-xs">السلة فارغة، امسح الباركود أو اضغط على الأصناف</p>
          </div>
        </div>

        <!-- Cart Summary & Actions -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/80 space-y-3 shrink-0">
          <div class="space-y-1.5 text-xs">
            <div class="flex justify-between text-slate-500">
              <span>المجموع الفرعي:</span>
              <span class="font-mono font-bold">{{ Number(subtotal).toLocaleString('ar-EG') }} ج.م</span>
            </div>
            <div class="flex justify-between text-slate-500 items-center">
              <span>الخصم:</span>
              <input
                v-model.number="discount"
                type="number"
                min="0"
                class="w-20 px-2 py-0.5 text-left text-xs bg-white border border-slate-200 rounded font-mono"
              />
            </div>
            <div class="flex justify-between text-slate-800 font-black text-base pt-2 border-t border-slate-200">
              <span>الإجمالي النهائي:</span>
              <span class="text-emerald-600 font-mono">{{ Number(grandTotal).toLocaleString('ar-EG') }} ج.م</span>
            </div>
          </div>

          <!-- Checkout Button -->
          <button
            @click="openPaymentModal"
            :disabled="cart.length === 0"
            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <span>💳 إتمام الدفع (F4)</span>
            <span class="font-mono text-xs opacity-80">({{ Number(grandTotal).toLocaleString('ar-EG') }} ج.م)</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Quick Payment Modal -->
    <div
      v-if="showPaymentModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
    >
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5" dir="rtl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-base font-black text-slate-800">إتمام عملية الدفع</h3>
          <button @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-center">
          <span class="text-xs font-bold text-emerald-800">المبلغ المطلوب سداده</span>
          <p class="text-3xl font-black text-emerald-600 mt-1 font-mono">
            {{ Number(grandTotal).toLocaleString('ar-EG') }} ج.م
          </p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ المستلم من العميل</label>
          <input
            v-model.number="cashReceived"
            type="number"
            class="w-full px-4 py-2.5 text-lg font-mono font-bold text-center border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none"
            placeholder="0"
          />

          <!-- Quick Cash Buttons -->
          <div class="flex gap-2 mt-2">
            <button
              v-for="amt in [50, 100, 200, 500]"
              :key="amt"
              type="button"
              @click="cashReceived = amt"
              class="flex-1 py-1 text-xs font-bold bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 cursor-pointer"
            >
              {{ amt }}
            </button>
            <button
              type="button"
              @click="cashReceived = grandTotal"
              class="flex-1 py-1 text-xs font-bold bg-emerald-100 hover:bg-emerald-200 rounded-lg text-emerald-800 cursor-pointer"
            >
              المضبوط
            </button>
          </div>
        </div>

        <div v-if="cashReceived > grandTotal" class="p-3 bg-blue-50 border border-blue-100 rounded-xl flex justify-between items-center text-sm font-bold text-blue-900">
          <span>الباقي للعميل:</span>
          <span class="font-mono text-base text-blue-700">{{ Number(cashReceived - grandTotal).toLocaleString('ar-EG') }} ج.م</span>
        </div>

        <div class="pt-2 flex gap-2">
          <button
            type="button"
            @click="showPaymentModal = false"
            class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl cursor-pointer"
          >
            إلغاء
          </button>
          <button
            type="button"
            @click="processCheckout"
            :disabled="isProcessing"
            class="flex-2 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl shadow-md cursor-pointer disabled:opacity-50"
          >
            {{ isProcessing ? 'جاري الحفظ والطباعة...' : 'تأكيد وحفظ الفاتورة ✓' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  categories: Array,
  recentItems: Array,
  funds: Array,
  clients: Array,
  defaultFund: Object,
});

const barcodeInput = ref(null);
const barcodeScan = ref('');
const activeCategory = ref(0);
const cart = ref([]);
const discount = ref(0);
const showPaymentModal = ref(false);
const cashReceived = ref(0);
const isProcessing = ref(false);
const currentTime = ref('');

// Clock
function updateClock() {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
let clockInterval = null;

onMounted(() => {
  updateClock();
  clockInterval = setInterval(updateClock, 1000);
  window.addEventListener('keydown', handleGlobalShortcuts);
});

onUnmounted(() => {
  clearInterval(clockInterval);
  window.removeEventListener('keydown', handleGlobalShortcuts);
});

function handleGlobalShortcuts(e) {
  if (e.key === 'F2') {
    e.preventDefault();
    barcodeInput.value?.focus();
  } else if (e.key === 'F4') {
    e.preventDefault();
    if (cart.value.length > 0) openPaymentModal();
  } else if (e.key === 'F9') {
    e.preventDefault();
    clearCart();
  }
}

const filteredItems = computed(() => {
  if (activeCategory.value === 0) return props.recentItems;
  return props.recentItems.filter(i => i.group1 === activeCategory.value);
});

const subtotal = computed(() => {
  return cart.value.reduce((acc, row) => acc + (row.qty * row.price), 0);
});

const grandTotal = computed(() => {
  return Math.max(0, subtotal.value - (discount.value || 0));
});

function addToCart(item) {
  const existingIdx = cart.value.findIndex(r => r.id === item.id);
  if (existingIdx !== -1) {
    cart.value[existingIdx].qty++;
  } else {
    const defaultUnit = item.units?.[0];
    cart.value.push({
      id: item.id,
      iname: item.iname,
      barcode: item.barcode,
      price: defaultUnit ? defaultUnit.price1 : item.price1,
      cost_price: defaultUnit ? defaultUnit.cost_price : item.cost_price,
      u_val: defaultUnit ? defaultUnit.u_val : 1,
      selectedUnitName: defaultUnit?.unit?.uname || 'قطعة',
      units: item.units,
      qty: 1,
    });
  }
}

function increaseQty(idx) {
  cart.value[idx].qty++;
}

function decreaseQty(idx) {
  if (cart.value[idx].qty > 1) {
    cart.value[idx].qty--;
  } else {
    removeFromCart(idx);
  }
}

function removeFromCart(idx) {
  cart.value.splice(idx, 1);
}

function clearCart() {
  if (cart.value.length === 0) return;
  if (confirm('هل تريد مسح جميع الأصناف من السلة؟')) {
    cart.value = [];
    discount.value = 0;
  }
}

function handleBarcodeSubmit() {
  const code = barcodeScan.value.trim();
  if (!code) return;

  // Search in loaded recentItems first
  const found = props.recentItems.find(i => i.barcode === code || i.units?.some(u => u.unit_barcode === code));
  if (found) {
    addToCart(found);
    barcodeScan.value = '';
    return;
  }

  // Fallback to AJAX lookup
  axios.get('/pos/search', { params: { q: code } }).then(res => {
    if (res.data && res.data.length > 0) {
      addToCart(res.data[0]);
    } else {
      alert(`لم يتم العثور على صنف بالباركود: ${code}`);
    }
    barcodeScan.value = '';
  });
}

function openPaymentModal() {
  cashReceived.value = grandTotal.value;
  showPaymentModal.value = true;
}

function processCheckout() {
  if (cart.value.length === 0) return;

  isProcessing.value = true;

  const payload = {
    items: cart.value.map(r => ({
      item_id: r.id,
      qty: r.qty,
      u_val: r.u_val,
      price: r.price,
      cost_price: r.cost_price,
      discount: 0,
    })),
    fat_disc: discount.value || 0,
    fat_tax: 0,
    paid_amount: cashReceived.value || grandTotal.value,
    acc_fund: props.defaultFund?.id || 0,
    order_type: 'takeaway',
  };

  axios.post('/pos/checkout', payload)
    .then(res => {
      alert('✓ تم حفظ الفاتورة بنجاح!');
      cart.value = [];
      discount.value = 0;
      showPaymentModal.value = false;
      barcodeInput.value?.focus();
    })
    .catch(err => {
      alert('حدث خطأ أثناء حفظ الفاتورة: ' + (err.response?.data?.message || err.message));
    })
    .finally(() => {
      isProcessing.value = false;
    });
}
</script>
