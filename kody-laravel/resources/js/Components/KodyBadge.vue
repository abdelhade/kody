<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'gray' }, // gray, red, yellow, green, blue, indigo, purple, primary
    rounded: { type: Boolean, default: true }
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary': return 'bg-[#017E84]/10 text-[#017E84]';
        case 'red': return 'bg-red-100 text-red-800';
        case 'yellow': return 'bg-yellow-100 text-yellow-800';
        case 'green': return 'bg-emerald-100 text-emerald-800';
        case 'blue': return 'bg-blue-100 text-blue-800';
        case 'indigo': return 'bg-indigo-100 text-indigo-800';
        case 'purple': return 'bg-purple-100 text-purple-800';
        case 'gray':
        default: return 'bg-gray-100 text-gray-800';
    }
});
</script>

<template>
    <span :class="[
        'inline-flex items-center px-2.5 py-0.5 text-xs font-medium',
        rounded ? 'rounded-full' : 'rounded',
        variantClasses
    ]">
        <slot />
    </span>
</template>
