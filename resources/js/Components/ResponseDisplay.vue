<script setup>
import { computed } from 'vue';
import MarkdownIt from 'markdown-it';
import hljs from 'highlight.js';
import 'highlight.js/styles/atom-one-dark.css';

const props = defineProps({
    content: {
        type: String,
        default: '',
    },
});

const md = new MarkdownIt({
    highlight: (code, lang) => {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return `<pre class="hljs"><code>${hljs.highlight(code, { language: lang, ignoreIllegals: true }).value}</code></pre>`;
            } catch (e) {
                console.error('Highlight error:', e);
            }
        }
        return `<pre class="hljs"><code>${md.utils.escapeHtml(code)}</code></pre>`;
    },
});

const renderedContent = computed(() => {
    if (!props.content) return '';
    return md.render(props.content);
});
</script>

<template>
    <div v-if="content" class="mt-8">
        <h3 class="text-lg font-semibold text-white mb-4">Réponse</h3>
        <div
            class="prose prose-invert max-w-none bg-slate-800/50 border border-slate-700 rounded-lg p-6 text-slate-300"
            v-html="renderedContent"
        ></div>
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
