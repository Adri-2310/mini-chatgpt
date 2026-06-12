<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ModelSelector from './ModelSelector.vue';
import ExportButtons from './ExportButtons.vue';

const props = defineProps({
    conversationId: Number,
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
    <div class="border-b border-border bg-gradient-to-r from-card to-card/80 transition-colors shadow-sm">
        <!-- Ligne 1: Titre centré en haut -->
        <div class="text-center py-3 px-6 border-b border-border">
            <h1 class="text-sm font-semibold text-foreground truncate">
                {{ props.conversationTitle }}
            </h1>
        </div>

        <!-- Ligne 2: Boutons + Stats + ModelSelector -->
        <div class="flex items-center justify-between gap-6 px-6 py-3 max-w-4xl mx-auto w-full">
            <!-- Gauche: Boutons + Stats -->
            <div class="flex items-center gap-4">
                <!-- Boutons (Exports) -->
                <div class="flex gap-2">
                    <ExportButtons :conversation-id="conversationId" :conversation-title="conversationTitle" />
                </div>

                <!-- Stats -->
                <div v-if="conversationId" class="flex items-center gap-6 border-l border-border pl-4">
                    <!-- Messages -->
                    <div class="flex items-center gap-2">
                        <span class="text-base">💬</span>
                        <div>
                            <div class="text-xs font-semibold text-foreground">{{ stats.total_messages }}</div>
                            <div class="text-xs text-muted-foreground">Messages</div>
                        </div>
                    </div>

                    <!-- Tokens -->
                    <div class="flex items-center gap-2">
                        <span class="text-base">⚡</span>
                        <div>
                            <div class="text-xs font-semibold text-foreground">{{ stats.total_tokens.toLocaleString() }}</div>
                            <div class="text-xs text-muted-foreground">Tokens</div>
                        </div>
                    </div>

                    <!-- Coût -->
                    <div class="flex items-center gap-2">
                        <span class="text-base">💵</span>
                        <div>
                            <div class="text-xs font-semibold text-foreground">${{ stats.total_cost_usd.toFixed(4) }}</div>
                            <div class="text-xs text-muted-foreground">Coût</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Droite: ModelSelector -->
            <div class="w-48">
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
