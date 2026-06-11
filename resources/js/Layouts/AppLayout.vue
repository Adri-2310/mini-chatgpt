<script setup>
import { Head } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Nav from '@/Components/Nav.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

defineProps({
    title: String,
});
</script>

<template>
    <div>
        <Head :title="title" />

        <ToastNotification />

        <Banner />

        <div class="min-h-screen bg-background transition-colors">
            <Nav />

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-card/50 border-b border-border">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
