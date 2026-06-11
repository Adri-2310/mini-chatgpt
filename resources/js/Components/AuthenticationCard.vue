<script setup>
import ThemeToggle from '@/Components/ThemeToggle.vue';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0
        bg-background transition-colors duration-200">

        <div class="absolute top-4 right-4">
            <ThemeToggle />
        </div>

        <div class="mb-8">
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-8 py-8
            bg-card border border-border
            shadow-2xl overflow-hidden sm:rounded-xl
            transition-colors duration-200">
            <slot />
        </div>
    </div>
</template>
