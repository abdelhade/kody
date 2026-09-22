<template>
  <TopNavbarLayout>
    <div class="p-6 space-y-6">
      


      <!-- Key Metrics (KPIs) -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Sales Card -->
        <KodyCard class="relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up delay-100">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-500">إجمالي المبيعات (اليوم)</h3>
            <div class="p-2 bg-green-100 text-green-600 rounded-lg group-hover:scale-110 transition-transform">
              <CurrencyDollarIcon class="w-5 h-5" />
            </div>
          </div>
          <div class="text-3xl font-black text-gray-800 mb-1">
            {{ formatCurrency(stats.sales_today) }}
          </div>
          <div class="flex items-center text-xs text-green-500 font-bold">
            <ArrowTrendingUpIcon class="w-4 h-4 mr-1" />
            <span>+12% عن الأمس</span>
          </div>
          <!-- Background Decoration -->
          <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        </KodyCard>

        <!-- Cash In Card -->
        <KodyCard class="relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up delay-200">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-500">المقبوضات النقدية</h3>
            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg group-hover:scale-110 transition-transform">
              <BanknotesIcon class="w-5 h-5" />
            </div>
          </div>
          <div class="text-3xl font-black text-gray-800 mb-1">
            {{ formatCurrency(stats.cash_in) }}
          </div>
          <div class="flex items-center text-xs text-blue-500 font-bold">
            <ArrowTrendingUpIcon class="w-4 h-4 mr-1" />
            <span>حركة الصناديق ممتازة</span>
          </div>
        </KodyCard>

        <!-- Invoices Card -->
        <KodyCard class="relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up delay-300">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-500">عدد الفواتير (الشهر)</h3>
            <div class="p-2 bg-purple-100 text-purple-600 rounded-lg group-hover:scale-110 transition-transform">
              <DocumentTextIcon class="w-5 h-5" />
            </div>
          </div>
          <div class="text-3xl font-black text-gray-800 mb-1">
            <span class="count-up">{{ stats.invoices_count }}</span>
          </div>
          <div class="flex items-center text-xs text-purple-500 font-bold">
            <ChartBarIcon class="w-4 h-4 mr-1" />
            <span>نشاط المبيعات</span>
          </div>
        </KodyCard>

        <!-- Low Stock Alerts -->
        <KodyCard class="relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-fade-in-up delay-400 border-l-4 border-red-500">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-500">نواقص المخزون</h3>
            <div class="p-2 bg-red-100 text-red-600 rounded-lg group-hover:scale-110 transition-transform animate-pulse">
              <ExclamationTriangleIcon class="w-5 h-5" />
            </div>
          </div>
          <div class="text-3xl font-black text-gray-800 mb-1 text-red-600">
            {{ stats.low_stock_items }} صنف
          </div>
          <div class="flex items-center text-xs text-red-500 font-bold">
            <span>تجاوزت حد إعادة الطلب!</span>
          </div>
        </KodyCard>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up delay-500">
        <!-- Sales Bar Chart Simulation -->
        <KodyCard class="lg:col-span-2">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <ChartBarSquareIcon class="w-6 h-6 text-indigo-500" />
            تحليل المبيعات الأسبوعية
          </h3>
          <div class="flex items-end gap-3 h-64 mt-4">
            <div v-for="(day, index) in chartData" :key="index" class="flex-1 flex flex-col items-center gap-2 group">
              <!-- Animated Bar -->
              <div class="w-full bg-indigo-100 rounded-t-lg relative overflow-hidden h-full flex items-end">
                <div 
                  class="w-full bg-indigo-500 rounded-t-lg transition-all duration-1000 ease-out group-hover:bg-indigo-600"
                  :style="`height: ${loaded ? day.percentage : 0}%; transition-delay: ${index * 100}ms`"
                ></div>
                <!-- Tooltip -->
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <span class="bg-gray-800 text-white text-xs font-bold py-1 px-2 rounded">{{ formatCurrency(day.value) }}</span>
                </div>
              </div>
              <span class="text-xs font-bold text-gray-500">{{ day.label }}</span>
            </div>
          </div>
        </KodyCard>

        <!-- Low Stock Table -->
        <KodyCard class="overflow-y-auto max-h-96">
          <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 text-red-600">
            <ExclamationCircleIcon class="w-6 h-6" />
            أصناف تحتاج لطلب
          </h3>
          <ul class="space-y-3">
            <li v-for="item in lowStockItems" :key="item.id" class="flex items-center justify-between p-3 bg-red-50/50 rounded-xl hover:bg-red-50 transition-colors border border-red-100">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm text-red-500 border border-red-100">
                  <CubeIcon class="w-5 h-5" />
                </div>
                <div>
                  <h4 class="text-sm font-bold text-gray-800 line-clamp-1" :title="item.iname">{{ item.iname }}</h4>
                  <p class="text-xs font-medium text-gray-500">كود: {{ item.code }}</p>
                </div>
              </div>
              <div class="text-left">
                <div class="text-sm font-black text-red-600">{{ item.itmqty }}</div>
                <div class="text-[10px] font-bold text-gray-400">الكمية</div>
              </div>
            </li>
            <li v-if="!lowStockItems.length" class="text-center py-6 text-sm font-bold text-gray-400">
              المخزون بوضع ممتاز 🎉
            </li>
          </ul>
        </KodyCard>
      </div>
    </div>
  </TopNavbarLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import TopNavbarLayout from '@/Layouts/TopNavbarLayout.vue';
import KodyCard from '@/Components/KodyCard.vue';
import { 
  CurrencyDollarIcon, BanknotesIcon, DocumentTextIcon, ChartBarIcon,
  ExclamationTriangleIcon, ChartBarSquareIcon, ArrowTrendingUpIcon,
  ExclamationCircleIcon, CubeIcon
} from '@heroicons/vue/24/solid';

const props = defineProps({
  stats: Object,
  lowStockItems: Array,
  weeklySales: Array,
});

const loaded = ref(false);

const chartData = ref([
  { label: 'السبت', value: 12000, percentage: 60 },
  { label: 'الأحد', value: 15000, percentage: 75 },
  { label: 'الإثنين', value: 8000, percentage: 40 },
  { label: 'الثلاثاء', value: 22000, percentage: 100 },
  { label: 'الأربعاء', value: 18000, percentage: 90 },
  { label: 'الخميس', value: 25000, percentage: 85 },
  { label: 'الجمعة', value: 5000, percentage: 25 },
]);

// Real data integration if provided from backend
if (props.weeklySales && props.weeklySales.length > 0) {
    const maxVal = Math.max(...props.weeklySales.map(d => d.value)) || 1;
    chartData.value = props.weeklySales.map(d => ({
        label: d.label,
        value: d.value,
        percentage: (d.value / maxVal) * 100
    }));
}

onMounted(() => {
  setTimeout(() => {
    loaded.value = true;
  }, 100);
});

const formatCurrency = (val) => {
  if(!val) return '0.00 ر.س';
  return new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' }).format(val);
}
</script>

<style scoped>
/* Custom animations */
.animate-fade-in-up {
  animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  opacity: 0;
  transform: translateY(20px);
}

.animate-slide-right {
  animation: slideRight 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  opacity: 0;
  transform: translateX(-20px);
}

.animate-fade-in {
  animation: fadeIn 0.8s ease-out forwards;
  opacity: 0;
}

.animate-spin-slow {
  animation: spin 15s linear infinite;
}

.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }
.delay-300 { animation-delay: 300ms; }
.delay-400 { animation-delay: 400ms; }
.delay-500 { animation-delay: 500ms; }

@keyframes fadeInUp {
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideRight {
  to { opacity: 1; transform: translateX(0); }
}

@keyframes fadeIn {
  to { opacity: 1; }
}

@keyframes spin {
  to { transform: rotate(360deg) translateY(-3rem) translateX(-6rem); }
}
</style>
