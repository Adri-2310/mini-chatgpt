<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-6 py-2.5
    bg-primary text-primary-foreground border border-transparent rounded-lg
    font-semibold text-sm hover:opacity-90
    focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background
    disabled:opacity-50 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
