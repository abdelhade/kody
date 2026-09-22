<script setup>
defineProps({
    title: { type: String, default: null },
    noPadding: { type: Boolean, default: false }
});
</script>

<template>
    <div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
        <div v-if="title || $slots.header" class="px-4 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 v-if="title" class="text-lg font-semibold text-gray-800 leading-tight">{{ title }}</h3>
            <slot name="header"></slot>
        </div>
        <div :class="[noPadding ? '' : 'p-4 sm:p-6']">
            <slot />
        </div>
        <div v-if="$slots.footer" class="px-4 py-3 bg-gray-50 border-t border-gray-100 sm:px-6">
            <slot name="footer"></slot>
        </div>
    </div>
</template>
