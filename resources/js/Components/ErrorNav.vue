<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <nav class="sticky top-0 z-50 bg-background border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <Link :href="route('welcome')" class="flex items-center gap-2 hover:opacity-80 transition">
                        <span class="text-2xl">🌶️</span>
                        <span class="text-xl font-bold text-foreground">SaveurIA</span>
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>
