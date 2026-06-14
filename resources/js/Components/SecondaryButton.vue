<script setup>
import { Button } from '@/components/ui/ui/button'

defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <Button :type="type" variant="secondary" size="sm">
        <slot />
    </Button>
</template>
