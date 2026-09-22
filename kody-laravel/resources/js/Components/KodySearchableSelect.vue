<script setup>
import { computed, ref, watch } from 'vue';
import { Combobox, ComboboxInput, ComboboxButton, ComboboxOptions, ComboboxOption } from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    modelValue: { type: [String, Number, Object], default: null },
    options: { type: Array, default: () => [] },
    label: { type: String, default: null },
    error: { type: String, default: null },
    displayKey: { type: String, default: 'name' }, // What to show to the user
    valueKey: { type: String, default: 'id' },     // What to emit (if null, emits the whole object)
    placeholder: { type: String, default: 'اختر...' },
    disabled: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    returnObject: { type: Boolean, default: false } // If true, modelValue is the whole object, not just valueKey
});

const emit = defineEmits(['update:modelValue', 'change']);

const query = ref('');

// Filter options based on query
const filteredOptions = computed(() =>
    query.value === ''
        ? props.options
        : props.options.filter((option) => {
            const displayValue = String(option[props.displayKey] || '').toLowerCase();
            return displayValue.includes(query.value.toLowerCase());
        })
);

// Find the currently selected object based on modelValue
const selectedOption = computed({
    get: () => {
        if (props.modelValue === null || props.modelValue === undefined) return null;
        if (props.returnObject) return props.modelValue;
        return props.options.find(opt => opt[props.valueKey] === props.modelValue) || null;
    },
    set: (value) => {
        if (!value) {
            emit('update:modelValue', null);
            emit('change', null);
            return;
        }
        const valToEmit = props.returnObject ? value : value[props.valueKey];
        emit('update:modelValue', valToEmit);
        emit('change', value);
    }
});

const displayValueFn = (item) => {
    return item ? item[props.displayKey] : '';
};
</script>

<template>
    <div class="mb-4 relative">
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <Combobox v-model="selectedOption" :disabled="disabled" by="id">
            <div class="relative mt-1">
                <div class="relative w-full cursor-default overflow-hidden rounded-md bg-white text-right shadow-sm focus:outline-none sm:text-sm"
                     :class="[
                         error ? 'border border-red-300 ring-1 ring-red-500' : 'border border-gray-300 focus-within:ring-1 focus-within:ring-[#017E84] focus-within:border-[#017E84]',
                         disabled ? 'bg-gray-100 opacity-75 cursor-not-allowed' : ''
                     ]">
                    <ComboboxInput
                        class="w-full border-none py-2 pl-10 pr-3 text-sm leading-5 text-gray-900 focus:ring-0"
                        :displayValue="displayValueFn"
                        @change="query = $event.target.value"
                        :placeholder="placeholder"
                    />
                    <ComboboxButton class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </ComboboxButton>
                </div>
                
                <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0" @after-leave="query = ''">
                    <ComboboxOptions class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                        <div v-if="filteredOptions.length === 0 && query !== ''" class="relative cursor-default select-none py-2 px-4 text-gray-700 text-center">
                            لا توجد نتائج مطابقة
                        </div>

                        <ComboboxOption
                            v-for="item in filteredOptions"
                            :key="item[valueKey]"
                            :value="item"
                            v-slot="{ selected, active }"
                        >
                            <li class="relative cursor-default select-none py-2 pl-10 pr-4"
                                :class="{'bg-[#017E84] text-white': active, 'text-gray-900': !active}">
                                <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                    {{ item[displayKey] }}
                                </span>
                                <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3" :class="{'text-white': active, 'text-[#017E84]': !active}">
                                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                </span>
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
                </transition>
            </div>
        </Combobox>
        
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
