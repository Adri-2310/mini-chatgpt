<script setup>
import { Label } from '@/components/ui/ui/label'

defineProps({
    value: String,
});
</script>

<template>
    <Label class="block">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </Label>
</template>
