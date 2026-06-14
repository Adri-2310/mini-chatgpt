<script setup>
import { ref } from 'vue';
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent } from '@/components/ui/ui/dropdown-menu';

defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'bg-card'],
    },
});

const open = ref(false);
</script>

<template>
    <DropdownMenu v-model:open="open">
        <DropdownMenuTrigger as-child>
            <slot name="trigger" />
        </DropdownMenuTrigger>

        <DropdownMenuContent :align="align === 'left' ? 'start' : 'end'" class="w-48 bg-card border border-border">
            <slot name="content" />
        </DropdownMenuContent>
    </DropdownMenu>
</template>
