<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
    activeConversationId: {
        type: [Number, null],
        default: null,
    },
});

const emit = defineEmits(['select', 'new', 'delete']);

const searchQuery = ref('');
const conversationToDelete = ref(null);
const showDeleteConfirm = ref(false);

const filteredConversations = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.conversations;
    }
    return props.conversations.filter(conv =>
        conv.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

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

const confirmDelete = (conversation) => {
    conversationToDelete.value = conversation;
    showDeleteConfirm.value = true;
};

const deleteConversation = async () => {
    if (conversationToDelete.value) {
        emit('delete', conversationToDelete.value.id);
        showDeleteConfirm.value = false;
        conversationToDelete.value = null;
    }
};
</script>

<template>
    <div class="h-screen w-80 bg-slate-900 border-r border-slate-700 flex flex-col">
        <!-- Header avec bouton nouveau -->
        <div class="p-4 border-b border-slate-700 space-y-3">
            <button
                @click="emit('new')"
                class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-500 hover:to-purple-500 transition font-medium text-sm"
            >
                + Nouvelle conversation
            </button>

            <!-- Barre de recherche -->
            <div class="relative">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Rechercher..."
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 text-white placeholder-slate-500 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:ring-offset-0 transition"
                />
                <svg v-if="!searchQuery" class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Liste des conversations -->
        <div class="flex-1 overflow-y-auto">
            <template v-if="conversations.length === 0">
                <div class="p-4 text-center text-slate-400 text-sm">
                    Aucune conversation
                </div>
            </template>

            <template v-else-if="filteredConversations.length === 0">
                <div class="p-4 text-center text-slate-400 text-sm">
                    Aucune conversation trouvée
                </div>
            </template>

            <template v-else>
                <div
                    v-for="conversation in filteredConversations"
                    :key="conversation.id"
                    :class="[
                        'group flex items-center justify-between px-4 py-3 border-b border-slate-800 hover:bg-slate-800 transition cursor-pointer',
                        activeConversationId === conversation.id
                            ? 'bg-slate-700 border-l-4 border-l-blue-600'
                            : ''
                    ]"
                    @click="emit('select', conversation.id)"
                >
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-white text-sm truncate">
                            {{ conversation.title }}
                        </div>
                        <div class="text-xs text-slate-400 mt-1">
                            {{ formatDate(conversation.updated_at) }}
                        </div>
                    </div>

                    <!-- Bouton de suppression -->
                    <button
                        @click.stop="confirmDelete(conversation)"
                        class="opacity-0 group-hover:opacity-100 transition ml-2 p-1 hover:bg-slate-600 rounded text-slate-400 hover:text-red-400"
                        title="Supprimer"
                    >
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 max-w-sm mx-4 space-y-4">
            <h3 class="text-lg font-medium text-white">
                Supprimer la conversation
            </h3>
            <p class="text-slate-400 text-sm">
                Êtes-vous sûr de vouloir supprimer "<strong>{{ conversationToDelete?.title }}</strong>" ? Cette action est irréversible.
            </p>
            <div class="flex gap-3 justify-end">
                <button
                    @click="showDeleteConfirm = false"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition text-sm"
                >
                    Annuler
                </button>
                <button
                    @click="deleteConversation"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</template>
