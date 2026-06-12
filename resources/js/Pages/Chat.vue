<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useStream } from '@laravel/stream-vue';
import ChatLayout from '../Layouts/ChatLayout.vue';
import ConversationList from '../Components/ConversationList.vue';
import ChatHeader from '../Components/ChatHeader.vue';
import MessageList from '../Components/MessageList.vue';
import MessageInput from '../Components/MessageInput.vue';

defineOptions({
  layout: ChatLayout,
  layoutProps: {
    title: 'Conversations'
  }
});

const props = defineProps({
    conversations: Array,
    models: Array,
});

const page = usePage();
const conversations = ref(props.conversations);
const activeConversationId = ref(null);
const messages = ref([]);
const selectedModel = ref('openai/gpt-4o-mini');
const error = ref(null);
const isConversationStarted = ref(false);
const messageListContainer = ref(null);
const sidebarOpen = ref(true);

// Création du buffer pour le streaming
let streamBuffer = '';

const scrollToBottom = () => {
    if (messageListContainer.value) {
        setTimeout(() => {
            messageListContainer.value.scrollTop = messageListContainer.value.scrollHeight;
        }, 0);
    }
};

const chatHeaderRef = ref(null);

const activeConversation = computed(() => {
    return conversations.value.find((c) => c.id === activeConversationId.value);
});

const conversationTitle = computed(() => {
    return activeConversation.value?.title || 'Nouvelle conversation';
});

const createNewConversation = async () => {
    try {
        const response = await fetch('/conversations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token,
            },
            body: JSON.stringify({
                model: selectedModel.value,
            }),
        });

        const data = await response.json();
        if (response.ok) {
            conversations.value.unshift(data);
            activeConversationId.value = data.id;
            messages.value = [];
            isConversationStarted.value = false;
            error.value = null;

            // Refetch stats pour la nouvelle conversation
            if (chatHeaderRef.value?.fetchStats) {
                await chatHeaderRef.value.fetchStats();
            }
        } else {
            error.value = data.error || 'Une erreur est survenue';
        }
    } catch (err) {
        error.value = 'Une erreur est survenue lors de la création de la conversation';
        console.error('Error creating conversation:', err);
    }
};

const selectConversation = async (conversationId) => {
    try {
        activeConversationId.value = conversationId;
        const response = await fetch(`/conversations/${conversationId}`, {});

        if (response.ok) {
            const data = await response.json();
            messages.value = data.messages || [];
            isConversationStarted.value = (messages.value.length > 0);

            // Restaurer le modèle utilisé pour cette conversation
            if (data.conversation?.model_used) {
                selectedModel.value = data.conversation.model_used;
            }

            // Refetch stats
            if (chatHeaderRef.value?.fetchStats) {
                await chatHeaderRef.value.fetchStats();
            }

            error.value = null;
            scrollToBottom();
        } else {
            error.value = 'Erreur lors du chargement de la conversation';
        }
    } catch (err) {
        error.value = 'Erreur lors du chargement de la conversation';
        console.error('Error loading conversation:', err);
    }
};

const { isStreaming, send: sendStream } = useStream(
    () => `/conversations/${activeConversationId.value}/messages/stream`,
    {
        headers: {
            'X-CSRF-TOKEN': page.props.csrf_token,
        },
        onData: (rawData) => {
            // Ajout au buffer et découpage
            streamBuffer += rawData;
            const lines = streamBuffer.split('\n');
            streamBuffer = lines.pop() || '';

            for (const line of lines) {
                if (line.trim() === '') continue;

                if (line.includes('[DONE]')) {
                    return;
                }

                if (line.startsWith('data: ')) {
                    const jsonStr = line.substring(6);
                    try {
                        const data = JSON.parse(jsonStr);
                        if (data.content) {
                            // On cible le message de l'assistant (le dernier de la liste)
                            const lastMessage = messages.value[messages.value.length - 1];
                            if (lastMessage && lastMessage.role === 'assistant') {
                                // On ajoute la lettre générée !
                                lastMessage.content += data.content;
                            }
                        }
                    } catch (e) {
                        console.error('Erreur de parsing JSON sur un chunk:', e);
                    }
                }
            }

            scrollToBottom();
        },
        onFinish: async () => {
            streamBuffer = '';
            isConversationStarted.value = true;

            try {
                const response = await fetch(`/conversations/${activeConversationId.value}`, {});
                if (response.ok) {
                    const data = await response.json();
                    // Remplacer les messages avec ceux de la BD qui contiennent tokens_used
                    messages.value = data.messages || [];

                    const index = conversations.value.findIndex(c => c.id === activeConversationId.value);
                    if (index > -1) {
                        conversations.value[index] = {
                            ...conversations.value[index],
                            title: data.conversation.title,
                            updated_at: data.conversation.updated_at,
                        };
                    }
                }
            } catch (err) {
                console.error('Erreur lors de la mise à jour de la conversation:', err);
            }

            // Refetch les stats après le streaming
            if (chatHeaderRef.value && chatHeaderRef.value.fetchStats) {
                await chatHeaderRef.value.fetchStats();
            }

            conversations.value.sort(
                (a, b) => new Date(b.updated_at) - new Date(a.updated_at)
            );

            scrollToBottom();
        },
        onError: (err) => {
            error.value = 'Erreur lors du streaming: ' + err;
            console.error('Stream error:', err);
        },
    }
);

const handleMessageSubmit = async (content) => {
    if (!activeConversationId.value) {
        return;
    }

    error.value = null;
    streamBuffer = '';
    isConversationStarted.value = true;

    const userMessage = {
        id: Date.now(),
        role: 'user',
        content: content,
        created_at: new Date().toISOString(),
    };
    messages.value.push(userMessage);

    const assistantMessage = {
        id: Date.now() + 1,
        role: 'assistant',
        content: '',
        created_at: new Date().toISOString(),
    };
    messages.value.push(assistantMessage);

    sendStream({
        content: content,
        model: selectedModel.value,
    });
};

const deleteConversation = async (conversationId) => {
    try {
        const response = await fetch(`/conversations/${conversationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token,
            },
        });

        if (response.ok) {
            // Retirer de la liste
            const index = conversations.value.findIndex(c => c.id === conversationId);
            if (index > -1) {
                conversations.value.splice(index, 1);
            }

            // Si c'était la conversation active, réinitialiser
            if (activeConversationId.value === conversationId) {
                activeConversationId.value = null;
                messages.value = [];
            }

            error.value = null;
        } else {
            error.value = 'Erreur lors de la suppression';
        }
    } catch (err) {
        error.value = 'Erreur lors de la suppression de la conversation';
        console.error('Error deleting conversation:', err);
    }
};
</script>

<template>
    <div class="flex h-full bg-background">
        <!-- Sidebar -->
        <div
            :class="[
                'transition-all duration-300 border-r border-border bg-sidebar hidden md:flex flex-col',
                sidebarOpen ? 'w-60' : 'w-[70px]'
            ]"
        >
            <ConversationList
                :conversations="conversations"
                :active-conversation-id="activeConversationId"
                :sidebar-open="sidebarOpen"
                @select="selectConversation"
                @new="createNewConversation"
                @delete="deleteConversation"
                @toggle-sidebar="sidebarOpen = !sidebarOpen"
            />
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
                <ChatHeader
                    ref="chatHeaderRef"
                    v-if="activeConversationId"
                    :conversation-id="activeConversationId"
                    :conversation-title="conversationTitle"
                    :sidebar-open="sidebarOpen"
                    :selected-model="selectedModel"
                    :models="models"
                    :model-disabled="isConversationStarted"
                    @update:selected-model="selectedModel = $event"
                />

                <div v-if="error" class="px-4 py-3 bg-destructive/10 border border-destructive text-destructive text-sm mx-4 mt-4 rounded">
                    {{ error }}
                </div>

                <template v-if="!activeConversationId">
                    <div class="flex-1 flex flex-col items-center justify-center">
                        <div class="text-center max-w-md px-8">
                            <div class="text-5xl mb-6">👋</div>
                            <h2 class="text-3xl font-bold text-foreground mb-4">Bienvenue!</h2>
                            <p class="text-muted-foreground text-lg mb-8">
                                Commencez une nouvelle conversation ou sélectionnez-en une existante pour continuer.
                            </p>
                            <button
                                @click="createNewConversation"
                                class="px-8 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition font-semibold"
                            >
                                + Créer une conversation
                            </button>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div ref="messageListContainer" class="flex-1 overflow-y-auto">
                        <MessageList :messages="messages" :loading="isStreaming" />
                    </div>

                    <MessageInput
                        :disabled="!activeConversationId || isStreaming"
                        @submit="handleMessageSubmit"
                    />
                </template>
            </div>
        </div>
</template>