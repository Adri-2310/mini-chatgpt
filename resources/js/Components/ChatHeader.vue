<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ModelSelector from './ModelSelector.vue';
import ExportButtons from './ExportButtons.vue';

const props = defineProps({
    conversationId: Number,
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

const page = usePage();
const stats = ref({
    total_messages: 0,
    total_tokens: 0,
    total_cost_usd: 0,
});

const fetchStats = async () => {
    if (!props.conversationId) {
        stats.value = { total_messages: 0, total_tokens: 0, total_cost_usd: 0 };
        return;
    }

    try {
        const response = await fetch(`/conversations/${props.conversationId}/stats`, {
            headers: { 'X-CSRF-TOKEN': page.props.csrf_token },
        });

        if (response.ok) {
            stats.value = await response.json();
        }
    } catch (err) {
        console.error('Erreur stats:', err);
    }
};

defineExpose({
    fetchStats,
});
</script>

<template>
    <div class="border-b border-border bg-card/50 transition-colors">
        <!-- Info Panel: Exports + Stats + ModelSelector en une ligne -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 md:gap-6 px-3 md:px-6 py-3 max-w-full overflow-x-auto">
            <!-- Gauche: Boutons (Exports) -->
            <div class="flex gap-2">
                <ExportButtons :conversation-id="conversationId" :conversation-title="`Conversation`" />
            </div>

            <!-- Centre-Gauche: Stats -->
            <div v-if="conversationId" class="flex items-center gap-6">
                <!-- Messages -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-foreground">💬</span>
                    <span class="text-sm font-medium text-foreground">{{ stats.total_messages }}</span>
                </div>

                <!-- Tokens -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-foreground">⚡</span>
                    <span class="text-sm font-medium text-foreground">{{ stats.total_tokens.toLocaleString() }}</span>
                </div>

                <!-- Coût -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-foreground">💵</span>
                    <span class="text-sm font-medium text-foreground">${{ stats.total_cost_usd.toFixed(4) }}</span>
                </div>
            </div>

            <!-- Droite: ModelSelector -->
            <div class="ml-auto">
                <ModelSelector
                    :model-value="selectedModel"
                    :models="models"
                    :disabled="modelDisabled"
                    @update:model-value="emit('update:selectedModel', $event)"
                />
            </div>
        </div>
    </div>
</template>
