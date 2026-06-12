<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    conversationId: Number,
});

const page = usePage();
const stats = ref({
    total_messages: 0,
    total_tokens: 0,
    total_cost_usd: 0,
});
const loading = ref(false);

const fetchStats = async () => {
    if (!props.conversationId) {
        stats.value = {
            total_messages: 0,
            total_tokens: 0,
            total_cost_usd: 0,
        };
        return;
    }

    loading.value = true;
    try {
        const response = await fetch(`/conversations/${props.conversationId}/stats`, {
            headers: {
                'X-CSRF-TOKEN': page.props.csrf_token,
            },
        });

        if (response.ok) {
            const data = await response.json();
            stats.value = {
                total_messages: data.total_messages || 0,
                total_tokens: data.total_tokens || 0,
                total_cost_usd: data.total_cost_usd || 0,
            };
            console.log('Stats fetched:', stats.value);
        } else {
            console.error('Failed to fetch stats:', response.status);
        }
    } catch (err) {
        console.error('Erreur lors du chargement des stats:', err);
    } finally {
        loading.value = false;
    }
};

watch(() => props.conversationId, (newId) => {
    console.log('Conversation ID changed to:', newId);
    fetchStats();
}, { immediate: true });

// Expose fetchStats so parent can call it
defineExpose({
    fetchStats,
});
</script>

<template>
    <div v-if="conversationId" class="px-4 py-3 bg-card border-t border-border text-sm text-muted-foreground">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <div class="font-semibold text-foreground">{{ stats.total_messages }}</div>
                <div class="text-xs">Messages</div>
            </div>
            <div>
                <div class="font-semibold text-foreground">{{ stats.total_tokens.toLocaleString() }}</div>
                <div class="text-xs">Tokens</div>
            </div>
            <div>
                <div class="font-semibold text-foreground">{{ (stats.total_tokens / 1000).toFixed(1) }}K</div>
                <div class="text-xs">Total</div>
            </div>
            <div>
                <div class="font-semibold text-foreground">${{ stats.total_cost_usd.toFixed(4) }}</div>
                <div class="text-xs">Coût USD</div>
            </div>
        </div>
    </div>
</template>
