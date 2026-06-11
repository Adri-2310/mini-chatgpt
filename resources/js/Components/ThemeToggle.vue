<script setup>
import { inject, ref, computed, onMounted } from 'vue';

const theme = inject('theme');
const forceUpdate = ref(0);

const isDark = computed(() => {
    // Trigger re-render when forceUpdate changes
    forceUpdate.value;

    if (typeof document !== 'undefined') {
        return document.documentElement.classList.contains('dark');
    }
    return false;
});

const toggleTheme = () => {
    if (theme && theme.toggleTheme) {
        theme.toggleTheme();
        // Forcer la re-render
        forceUpdate.value++;
    }
};

onMounted(() => {
    // Ajouter un listener pour les changements du thème
    const observer = new MutationObserver(() => {
        forceUpdate.value++;
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    });
});
</script>

<template>
    <button
        @click="toggleTheme"
        class="p-2 rounded-lg transition-colors
        bg-secondary text-foreground
        hover:opacity-80
        focus:outline-none focus:ring-2 focus:ring-ring"
        :title="isDark ? 'Passer en mode clair' : 'Passer en mode sombre'"
    >
        <svg v-if="isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
        <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l-2.12-2.12a4 4 0 00 5.656-5.656l2.12 2.12a6 6 0 01-5.656 5.656zm2.12-10.607a6 6 0 01 0 8.485l-2.12-2.12a4 4 0 005.656-5.656l-2.12-2.12zM10 18a1 1 0 11-2 0v-1a1 1 0 112 0v1z" clip-rule="evenodd" />
        </svg>
    </button>
</template>
