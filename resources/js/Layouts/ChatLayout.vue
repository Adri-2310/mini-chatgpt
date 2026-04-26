<script setup>
import { Head } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Nav from '@/Components/Nav.vue';

defineProps({
    title: String,
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900">
        <Head :title="title" />

        <Banner />

        <Nav />

        <!-- Fullscreen content (no padding/max-width wrapper) -->
        <div class="h-[calc(100vh-64px)]">
            <slot />
        </div>
    </div>
</template>
