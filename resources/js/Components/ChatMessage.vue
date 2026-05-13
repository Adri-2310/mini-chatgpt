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
    <div :class="['flex flex-col mb-4', props.role === 'user' ? 'items-end' : 'items-start']">
        <div
            :class="[
                'max-w-xs lg:max-w-md px-4 py-3 rounded-lg',
                props.role === 'user'
                    ? 'bg-blue-600 text-white'
                    : 'bg-slate-700 text-slate-100'
            ]"
        >
            <div v-if="props.role === 'assistant'" class="prose prose-invert max-w-none text-sm">
                <div v-html="formatContent(props.content)"></div>
            </div>
            <div v-else class="text-sm">
                {{ props.content }}
            </div>
        </div>
        <div v-if="props.role === 'assistant' && props.tokensUsed" class="text-xs text-gray-400 mt-1">
            {{ props.tokensUsed }} tokens
        </div>
    </div>
</template>

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
