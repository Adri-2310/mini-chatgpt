<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
    activeConversationId: {
        type: [Number, null],
        default: null,
    },
    sidebarOpen: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['select', 'new', 'delete', 'toggle-sidebar']);

const page = usePage();
const searchQuery = ref('');
const conversationToDelete = ref(null);
const showDeleteConfirm = ref(false);
const conversationToEdit = ref(null);
const editTitle = ref('');
const showEditModal = ref(false);

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

const startEditConversation = (conversation) => {
    conversationToEdit.value = conversation;
    editTitle.value = conversation.title;
    showEditModal.value = true;
};

const updateConversationTitle = async () => {
    if (!conversationToEdit.value || !editTitle.value.trim()) {
        return;
    }

    try {
        const response = await fetch(`/conversations/${conversationToEdit.value.id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token,
            },
            body: JSON.stringify({
                title: editTitle.value.trim(),
            }),
        });

        if (response.ok) {
            conversationToEdit.value.title = editTitle.value.trim();
            showEditModal.value = false;
            conversationToEdit.value = null;
        }
    } catch (err) {
        console.error('Erreur lors de la modification:', err);
    }
};
</script>

<template>
    <div class="h-full bg-sidebar border-r border-sidebar-border flex flex-col transition-colors">
        <!-- Header avec bouton nouveau -->
        <div :class="['p-3 border-b border-sidebar-border space-y-3', sidebarOpen ? '' : 'flex justify-center']">
            <button
                @click="emit('new')"
                :class="[
                    'px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition font-medium text-sm',
                    sidebarOpen ? 'w-full' : 'p-2'
                ]"
                :title="sidebarOpen ? '' : 'Nouvelle conversation'"
            >
                <span v-if="sidebarOpen">+ Nouvelle conversation</span>
                <svg v-else class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>

            <!-- Barre de recherche (visible seulement quand ouvert) -->
            <div v-if="sidebarOpen" class="relative">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Rechercher..."
                    class="w-full px-3 py-2 bg-input border border-border text-foreground placeholder-muted-foreground rounded-lg text-sm focus:border-sidebar-primary focus:ring-1 focus:ring-sidebar-primary focus:ring-offset-0 transition"
                />
                <svg v-if="!searchQuery" class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Liste des conversations (visible seulement quand ouvert) -->
        <div v-if="sidebarOpen" class="flex-1 overflow-y-auto">
            <template v-if="conversations.length === 0">
                <div class="p-4 text-center text-sidebar-foreground text-sm">
                    Aucune conversation
                </div>
            </template>

            <template v-else-if="filteredConversations.length === 0">
                <div class="p-4 text-center text-sidebar-foreground text-sm">
                    Aucune conversation trouvée
                </div>
            </template>

            <template v-else>
                <div
                    v-for="conversation in filteredConversations"
                    :key="conversation.id"
                    :class="[
                        'group flex items-center justify-between px-4 py-3 border-b border-sidebar-border hover:bg-sidebar-accent transition cursor-pointer',
                        activeConversationId === conversation.id
                            ? 'bg-sidebar-accent border-l-4 border-l-sidebar-primary'
                            : ''
                    ]"
                    @click="emit('select', conversation.id)"
                >
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sidebar-foreground text-sm truncate">
                            {{ conversation.title }}
                        </div>
                        <div class="text-xs text-muted-foreground mt-1">
                            {{ formatDate(conversation.updated_at) }}
                        </div>
                    </div>

                    <!-- Boutons d'édition et suppression -->
                    <button
                        @click.stop="startEditConversation(conversation)"
                        class="opacity-0 group-hover:opacity-100 transition ml-2 p-1 hover:bg-card rounded text-muted-foreground hover:text-sidebar-primary"
                        title="Éditer"
                    >
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>

                    <button
                        @click.stop="confirmDelete(conversation)"
                        class="opacity-0 group-hover:opacity-100 transition ml-2 p-1 hover:bg-card rounded text-muted-foreground hover:text-destructive"
                        title="Supprimer"
                    >
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <!-- Toggle Button (bas de la sidebar) -->
        <div class="border-t border-sidebar-border p-2">
            <button
                @click="emit('toggle-sidebar')"
                class="w-full p-2 hover:bg-sidebar-accent rounded transition text-sidebar-foreground hover:text-sidebar-primary flex items-center justify-center"
                :title="sidebarOpen ? 'Fermer la sidebar' : 'Ouvrir la sidebar'"
            >
                <svg v-if="sidebarOpen" class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <svg v-else class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Modal d'édition -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-card border border-border rounded-lg p-6 max-w-sm mx-4 space-y-4">
            <h3 class="text-lg font-medium text-foreground">
                Éditer la conversation
            </h3>
            <input
                v-model="editTitle"
                type="text"
                class="w-full px-3 py-2 bg-input border border-border text-foreground placeholder-muted-foreground rounded-lg focus:border-primary focus:ring-1 focus:ring-primary focus:ring-offset-0 transition text-sm"
                placeholder="Titre de la conversation"
                autofocus
            />
            <div class="flex gap-3 justify-end">
                <button
                    @click="showEditModal = false"
                    class="px-4 py-2 bg-secondary hover:bg-secondary/80 text-foreground rounded-lg transition text-sm"
                >
                    Annuler
                </button>
                <button
                    @click="updateConversationTitle"
                    class="px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition text-sm"
                >
                    Enregistrer
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-card border border-border rounded-lg p-6 max-w-sm mx-4 space-y-4">
            <h3 class="text-lg font-medium text-foreground">
                Supprimer la conversation
            </h3>
            <p class="text-muted-foreground text-sm">
                Êtes-vous sûr de vouloir supprimer "<strong>{{ conversationToDelete?.title }}</strong>" ? Cette action est irréversible.
            </p>
            <div class="flex gap-3 justify-end">
                <button
                    @click="showDeleteConfirm = false"
                    class="px-4 py-2 bg-secondary hover:bg-secondary/80 text-foreground rounded-lg transition text-sm"
                >
                    Annuler
                </button>
                <button
                    @click="deleteConversation"
                    class="px-4 py-2 bg-destructive hover:bg-destructive/90 text-destructive-foreground rounded-lg transition text-sm"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</template>
