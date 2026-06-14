<script setup>
import { Dialog, DialogContent } from '@/components/ui/ui/dialog';
import { computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

const handleOpenChange = (open) => {
    if (!open && props.closeable) {
        emit('close');
    }
};

const maxWidthClass = computed(() => {
    return {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});
</script>

<template>
    <Dialog :open="show" @update:open="handleOpenChange">
        <DialogContent :class="maxWidthClass">
            <slot />
        </DialogContent>
    </Dialog>
</template>
