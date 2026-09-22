<script setup>
import { computed } from 'vue';

const props = defineProps({
    columns: { type: Array, required: true }, // Array of objects: { key, label, sortable, width }
    data: { type: Array, required: true },
    loading: { type: Boolean, default: false },
    emptyMessage: { type: String, default: 'لا توجد بيانات للعرض' }
});

const emit = defineEmits(['sort']);
</script>

<template>
    <div class="flex flex-col border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm relative">
        <div class="overflow-x-auto max-h-[600px]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th 
                            v-for="col in columns" 
                            :key="col.key"
                            scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap"
                            :class="[col.sortable ? 'cursor-pointer hover:bg-gray-100' : '']"
                            :style="col.width ? `width: ${col.width}` : ''"
                            @click="col.sortable ? emit('sort', col.key) : null"
                        >
                            <div class="flex items-center justify-start gap-1">
                                {{ col.label }}
                                <svg v-if="col.sortable" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 relative">
                    <tr v-if="loading" class="absolute inset-0 bg-white/50 z-20 flex items-center justify-center min-h-[100px]">
                        <svg class="animate-spin h-8 w-8 text-[#017E84]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </tr>

                    <template v-if="data.length > 0">
                        <tr v-for="(row, rowIndex) in data" :key="row.id || rowIndex" class="hover:bg-gray-50 transition-colors">
                            <td 
                                v-for="col in columns" 
                                :key="col.key"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                <!-- Support custom cell rendering via slots -->
                                <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                    {{ row[col.key] }}
                                </slot>
                            </td>
                        </tr>
                    </template>
                    <tr v-else-if="!loading">
                        <td :colspan="columns.length" class="px-6 py-8 text-center text-gray-500 text-sm">
                            {{ emptyMessage }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
