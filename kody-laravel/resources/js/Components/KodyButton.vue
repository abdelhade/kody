<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: { type: String, default: 'button' },
    variant: { type: String, default: 'primary' }, // primary, secondary, danger, ghost
    size: { type: String, default: 'md' }, // sm, md, lg
    loading: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

const baseClasses = 'inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'bg-[#017E84] text-white hover:bg-[#01686d] focus:ring-[#017E84]';
        case 'secondary':
            return 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500 border border-gray-300';
        case 'danger':
            return 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-600';
        case 'ghost':
            return 'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-500';
        default:
            return 'bg-[#017E84] text-white hover:bg-[#01686d] focus:ring-[#017E84]';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'px-3 py-1.5 text-sm';
        case 'lg':
            return 'px-6 py-3 text-lg';
        case 'md':
        default:
            return 'px-4 py-2 text-base';
    }
});
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="[baseClasses, variantClasses, sizeClasses]"
    >
        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <slot />
    </button>
</template>
