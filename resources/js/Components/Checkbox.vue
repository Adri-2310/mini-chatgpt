<script setup>
import { computed } from 'vue';
import { Checkbox } from '@/Components/ui/ui/checkbox'

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:checked']);

const modelValue = computed({
    get() {
        return props.checked;
    },
    set(newVal) {
        emit('update:checked', newVal);
    },
});
</script>

<template>
    <Checkbox
        v-model="modelValue"
        :value="value"
    />
</template>
