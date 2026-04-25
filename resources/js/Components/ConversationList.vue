<script setup>
import { defineEmits, defineProps } from 'vue';

defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
    activeConversationId: {
        type: [Number, null],
        default: null,
    },
});

const emit = defineEmits(['select', 'new']);

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Hier';
    } else {
        return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
    }
};
</script>

<template>
    <div class="h-screen w-80 bg-slate-900 border-r border-slate-700 flex flex-col">
        <div class="p-4 border-b border-slate-700">
            <button
                @click="emit('new')"
                class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-500 hover:to-purple-500 transition font-medium text-sm"
            >
                + Nouvelle conversation
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <template v-if="conversations.length === 0">
                <div class="p-4 text-center text-slate-400 text-sm">
                    Aucune conversation
                </div>
            </template>

            <template v-else>
                <button
                    v-for="conversation in conversations"
                    :key="conversation.id"
                    @click="emit('select', conversation.id)"
                    :class="[
                        'w-full text-left px-4 py-3 border-b border-slate-800 hover:bg-slate-800 transition',
                        activeConversationId === conversation.id
                            ? 'bg-slate-700 border-l-4 border-l-blue-600'
                            : ''
                    ]"
                >
                    <div class="font-medium text-white text-sm truncate">
                        {{ conversation.title }}
                    </div>
                    <div class="text-xs text-slate-400 mt-1">
                        {{ formatDate(conversation.updated_at) }}
                    </div>
                </button>
            </template>
        </div>
    </div>
</template>
