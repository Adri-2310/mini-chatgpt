<script setup>
import ModelSelector from './ModelSelector.vue';

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

const emit = defineEmits(['update:selectedModel']);
</script>

<template>
    <div class="border-b border-border bg-card/50 p-4 transition-colors">
        <div class="flex items-center justify-between gap-4">
            <div class="flex-1" />
            <div class="flex-1 text-center">
                <h1 class="text-xl font-semibold text-foreground truncate">
                    {{ props.conversationTitle }}
                </h1>
            </div>
            <div class="flex-1 flex justify-end">
                <div class="bg-secondary/50 rounded-lg px-4 py-3 border border-border">
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
