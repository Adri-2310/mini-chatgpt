<script setup>
import { ref, watchEffect, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/ui/alert';
import { Button } from '@/components/ui/ui/button';

const page = usePage();
const show = ref(true);
const style = ref('success');
const message = ref('');

watchEffect(() => {
    style.value = page.props.jetstream?.flash?.bannerStyle || 'success';
    message.value = page.props.jetstream?.flash?.banner || '';
    show.value = true;
});

const variant = computed(() => style.value === 'success' ? 'default' : 'destructive');
</script>

<template>
    <Alert v-if="show && message" :variant="variant" class="rounded-none">
        <svg v-if="style === 'success'" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-else class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        <div class="flex-1">
            <AlertTitle>{{ style === 'success' ? 'Succès' : 'Erreur' }}</AlertTitle>
            <AlertDescription>{{ message }}</AlertDescription>
        </div>
        <Button variant="ghost" size="sm" @click="show = false">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </Button>
    </Alert>
</template>
