<script setup>
import { Button } from '@/components/ui/ui/button'

defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <Button :type="type" variant="default">
        <slot />
    </Button>
</template>
