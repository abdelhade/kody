<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    label: { type: String, default: null },
    type: { type: String, default: 'text' },
    error: { type: String, default: null },
    id: { type: String, default: () => `input-${Math.random().toString(36).substr(2, 9)}` },
    placeholder: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="mb-4">
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input
                :id="id"
                ref="input"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :disabled="disabled"
                :placeholder="placeholder"
                :class="[
                    'block w-full rounded-md shadow-sm sm:text-sm transition-colors focus:outline-none',
                    error
                        ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                        : 'border-gray-300 focus:ring-[#017E84] focus:border-[#017E84]',
                    disabled ? 'bg-gray-100 cursor-not-allowed' : ''
                ]"
            />
        </div>
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
