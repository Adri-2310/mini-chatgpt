<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    conversationId: Number,
    modelValue: String,
});

const emit = defineEmits(['update:modelValue', 'search']);

const query = ref(props.modelValue || '');
const results = ref([]);
const resultCount = ref(0);
const isSearching = ref(false);

const performSearch = async () => {
    if (!props.conversationId || query.value.length < 2) {
        results.value = [];
        resultCount.value = 0;
        return;
    }

    isSearching.value = true;
    try {
        const response = await fetch(`/conversations/${props.conversationId}/search?q=${encodeURIComponent(query.value)}`);
        if (response.ok) {
            const data = await response.json();
            results.value = data.results;
            resultCount.value = data.count;
            emit('search', data);
        }
    } catch (err) {
        console.error('Erreur de recherche:', err);
    } finally {
        isSearching.value = false;
    }
};

watch(query, (newVal) => {
    emit('update:modelValue', newVal);
    performSearch();
}, { debounce: 300 });

const clear = () => {
    query.value = '';
    results.value = [];
    resultCount.value = 0;
};
</script>

<template>
    <div v-if="conversationId" class="px-4 py-3 bg-card border-b border-border">
        <div class="flex items-center gap-2">
            <input
                v-model="query"
                type="text"
                placeholder="🔍 Chercher dans la conversation..."
                class="flex-1 px-3 py-2 bg-input border border-border rounded-lg text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
            />
            <button
                v-if="query"
                @click="clear"
                class="px-3 py-2 text-muted-foreground hover:text-foreground transition"
            >
                ✕
            </button>
        </div>
        <div v-if="resultCount > 0" class="mt-2 text-xs text-muted-foreground">
            {{ resultCount }} résultat(s) trouvé(s)
        </div>
    </div>
</template>
