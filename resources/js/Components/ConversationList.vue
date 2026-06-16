<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui/ui/dialog';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle } from '@/Components/ui/ui/alert-dialog';
import { Button } from '@/Components/ui/ui/button';
import { Input } from '@/Components/ui/ui/input';

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
const conversationToDelete = ref(null);
const showDeleteConfirm = ref(false);
const conversationToEdit = ref(null);
const editTitle = ref('');
const showEditModal = ref(false);


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
        </div>

        <!-- Liste des conversations (visible seulement quand ouvert) -->
        <div v-if="sidebarOpen" class="flex-1 overflow-y-auto">
            <template v-if="conversations.length === 0">
                <div class="p-4 text-center text-sidebar-foreground text-sm">
                    Aucune conversation
                </div>
            </template>

            <template v-else-if="conversations.length === 0">
                <div class="p-4 text-center text-sidebar-foreground text-sm">
                    Aucune conversation trouvée
                </div>
            </template>

            <template v-else>
                <div
                    v-for="conversation in conversations"
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

    <!-- Dialog d'édition -->
    <Dialog v-model:open="showEditModal">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Éditer la conversation</DialogTitle>
            </DialogHeader>
            <Input
                v-model="editTitle"
                type="text"
                placeholder="Titre de la conversation"
                autofocus
            />
            <DialogFooter class="flex gap-3 justify-end">
                <Button variant="secondary" @click="showEditModal = false">
                    Annuler
                </Button>
                <Button @click="updateConversationTitle">
                    Enregistrer
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- AlertDialog de confirmation de suppression -->
    <AlertDialog v-model:open="showDeleteConfirm">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Supprimer la conversation</AlertDialogTitle>
                <AlertDialogDescription>
                    Êtes-vous sûr de vouloir supprimer "<strong>{{ conversationToDelete?.title }}</strong>" ? Cette action est irréversible.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Annuler</AlertDialogCancel>
                <AlertDialogAction @click="deleteConversation" class="bg-destructive hover:bg-destructive/90">
                    Supprimer
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
