<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    terms: String,
});
</script>

<template>
    <Head title="Conditions de Service" />

    <div class="font-sans text-slate-100 antialiased">
        <div class="pt-4 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 min-h-screen">
            <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-2xl mt-6 p-6 bg-slate-800 shadow-md overflow-hidden sm:rounded-lg prose prose-invert" v-html="terms" />
            </div>
        </div>
    </div>
</template>
