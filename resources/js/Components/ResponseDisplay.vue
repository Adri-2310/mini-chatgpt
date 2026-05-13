<script setup>
import { computed } from 'vue';
import { useMarkdown } from '@/composables/useMarkdown';
import 'highlight.js/styles/atom-one-dark.css';

const props = defineProps({
    content: {
        type: String,
        default: '',
    },
    tokensUsed: {
        type: Number,
        default: null,
    },
});

const { render } = useMarkdown();

const renderedContent = computed(() => {
    return render(props.content);
});
</script>

<template>
    <div v-if="content" class="mt-8">
        <h3 class="text-lg font-semibold text-white mb-4">Réponse</h3>
        <div
            class="prose prose-invert max-w-none bg-slate-800/50 border border-slate-700 rounded-lg p-6 text-slate-300"
            v-html="renderedContent"
        ></div>
        <div v-if="tokensUsed" class="text-xs text-gray-400 mt-2 text-right">
            {{ tokensUsed }} tokens
        </div>
    </div>
</template>

<style scoped>
:deep(.prose) {
    --tw-prose-body: rgb(226, 232, 240);
    --tw-prose-headings: rgb(241, 245, 249);
    --tw-prose-links: rgb(96, 165, 250);
    --tw-prose-code: rgb(226, 232, 240);
    --tw-prose-pre-bg: rgb(30, 41, 59);
}

:deep(.hljs) {
    background-color: rgb(15, 23, 42);
    border: 1px solid rgb(51, 65, 85);
    border-radius: 0.5rem;
    padding: 1rem;
}

:deep(.hljs code) {
    background-color: transparent;
    color: rgb(226, 232, 240);
}
</style>
