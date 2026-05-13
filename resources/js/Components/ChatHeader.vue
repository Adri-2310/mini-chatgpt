<script setup>
import ModelSelector from './ModelSelector.vue';
import { useMarkdown } from '@/composables/useMarkdown';

const props = defineProps({
    conversationTitle: {
        type: String,
        default: 'Nouvelle conversation',
    },
    selectedModel: {
        type: String,
        default: 'openai/gpt-4o-mini',
    },
    models: {
        type: Array,
        default: () => [
            { id: 'openai/gpt-4o-mini', name: 'GPT-4o mini' },
            { id: 'google/gemini-2.5-flash-exp', name: 'Gemini 2.5 Flash' },
            { id: 'anthropic/claude-3.5-haiku', name: 'Claude 3.5 Haiku' },
        ],
    },
    modelDisabled: {
        type: Boolean,
        default: false,
    },
});

const { render } = useMarkdown();
const emit = defineEmits(['update:selectedModel']);

const renderTitle = (title) => render(title);
</script>

<template>
    <div class="border-b border-slate-600 bg-slate-800/50 p-4">
        <div class="flex items-center justify-between gap-4">
            <div class="flex-1" />
            <div class="flex-1 text-center">
                <h1 class="text-xl font-semibold text-white truncate" v-html="renderTitle(props.conversationTitle)">
                </h1>
            </div>
            <div class="flex-1 flex justify-end">
                <div class="bg-slate-700/30 rounded-lg px-4 py-3 border border-slate-600/50">
                    <ModelSelector
                        :model-value="selectedModel"
                        :models="models"
                        :disabled="modelDisabled"
                        @update:model-value="emit('update:selectedModel', $event)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
