<script setup>
import ResponseDisplay from './ResponseDisplay.vue';

defineProps({
    role: {
        type: String,
        required: true,
    },
    content: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div :class="['flex mb-4', role === 'user' ? 'justify-end' : 'justify-start']">
        <div
            :class="[
                'max-w-xs lg:max-w-md px-4 py-3 rounded-lg',
                role === 'user'
                    ? 'bg-blue-600 text-white'
                    : 'bg-slate-700 text-slate-100'
            ]"
        >
            <div v-if="role === 'assistant'" class="prose prose-invert max-w-none text-sm">
                <div v-html="formatContent(content)"></div>
            </div>
            <div v-else class="text-sm">
                {{ content }}
            </div>
        </div>
    </div>
</template>

<script>
import MarkdownIt from 'markdown-it';
import hljs from 'highlight.js';

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

export default {
    methods: {
        formatContent(content) {
            if (this.role === 'assistant') {
                return md.render(content);
            }
            return content;
        },
    },
};
</script>

<style scoped>
:deep(.prose) {
    --tw-prose-body: rgb(226, 232, 240);
    --tw-prose-headings: rgb(241, 245, 249);
    --tw-prose-links: rgb(96, 165, 250);
    --tw-prose-code: rgb(226, 232, 240);
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
    font-size: 0.875rem;
}
</style>
