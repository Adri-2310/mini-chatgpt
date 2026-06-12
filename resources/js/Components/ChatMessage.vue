<script setup>
import { useMarkdown } from '@/composables/useMarkdown';

const props = defineProps({
    role: {
        type: String,
        required: true,
    },
    content: {
        type: String,
        required: true,
    },
    tokensUsed: {
        type: Number,
        default: null,
    },
});

const { render } = useMarkdown();

const formatContent = (content) => {
    if (props.role === 'assistant') {
        return render(content);
    }
    return content;
};
</script>

<template>
    <div :class="['flex flex-col mb-6 gap-1', props.role === 'user' ? 'items-end' : 'items-start']">
        <div
            :class="[
                'max-w-2xl lg:max-w-3xl px-5 py-3 rounded-lg shadow-sm transition-colors',
                props.role === 'user'
                    ? 'bg-accent text-accent-foreground'
                    : 'bg-card text-card-foreground border border-border'
            ]"
        >
            <div v-if="props.role === 'assistant'" class="prose prose-sm prose-invert max-w-none">
                <div v-html="formatContent(props.content)"></div>
            </div>
            <div v-else class="text-sm leading-relaxed">
                {{ props.content }}
            </div>
        </div>
        <div v-if="props.role === 'assistant' && props.tokensUsed" class="text-xs text-muted-foreground px-2">
            {{ props.tokensUsed }} tokens
        </div>
    </div>
</template>

<style scoped>
:deep(.prose) {
    color: inherit;
    font-size: inherit;
}

:deep(.prose p) {
    margin: 0.5rem 0;
}

:deep(.prose h1, .prose h2, .prose h3) {
    margin: 0.75rem 0 0.5rem 0;
    font-weight: 600;
}

:deep(.prose a) {
    color: currentColor;
    text-decoration: underline;
    opacity: 0.8;
}

:deep(.prose a:hover) {
    opacity: 1;
}

:deep(.prose code) {
    background-color: rgba(0, 0, 0, 0.2);
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-family: monospace;
    font-size: 0.875em;
}

:deep(.prose pre) {
    margin: 0.75rem 0;
    background-color: rgba(0, 0, 0, 0.3);
    border-radius: 0.5rem;
    overflow-x: auto;
}

:deep(.hljs) {
    background-color: rgba(0, 0, 0, 0.3) !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.5rem;
    padding: 1rem;
    color: inherit;
}

:deep(.hljs code) {
    background-color: transparent;
    color: inherit;
    font-size: 0.875rem;
}

:deep(.prose ul, .prose ol) {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
}

:deep(.prose li) {
    margin: 0.25rem 0;
}

:deep(.prose blockquote) {
    border-left: 3px solid currentColor;
    padding-left: 1rem;
    opacity: 0.8;
    margin: 0.5rem 0;
}
</style>
