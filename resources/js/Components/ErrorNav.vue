<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <nav class="sticky top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <Link :href="route('welcome')" class="flex items-center space-x-2 hover:opacity-80 transition">
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                            🤖 Mini-ChatGPT
                        </span>
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>
