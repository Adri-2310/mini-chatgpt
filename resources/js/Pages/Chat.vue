<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import ConversationList from '../Components/ConversationList.vue';
import ChatHeader from '../Components/ChatHeader.vue';
import MessageList from '../Components/MessageList.vue';
import MessageInput from '../Components/MessageInput.vue';

const props = defineProps({
    conversations: Array,
    models: Array,
});

const page = usePage();
const conversations = ref(props.conversations);
const activeConversationId = ref(null);
const messages = ref([]);
const selectedModel = ref('openai/gpt-4o-mini');
const loading = ref(false);
const error = ref(null);
const isConversationStarted = ref(false);

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
                'X-CSRF-Token': page.props.csrf_token,
            },
            body: JSON.stringify({}),
        });

        const data = await response.json();
        if (response.ok) {
            conversations.value.unshift(data);
            activeConversationId.value = data.id;
            messages.value = [];
            isConversationStarted.value = false;
            error.value = null;
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
        const response = await fetch(`/conversations/${conversationId}`);

        if (response.ok) {
            const data = await response.json();
            messages.value = data.messages || [];
            isConversationStarted.value = (messages.value.length > 0);
            error.value = null;
        } else {
            error.value = 'Erreur lors du chargement de la conversation';
        }
    } catch (err) {
        error.value = 'Erreur lors du chargement de la conversation';
        console.error('Error loading conversation:', err);
    }
};

const handleMessageSubmit = async (content) => {
    if (!activeConversationId.value) {
        return;
    }

    loading.value = true;
    error.value = null;
    try {
        const response = await fetch(`/conversations/${activeConversationId.value}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': page.props.csrf_token,
            },
            body: JSON.stringify({
                content,
                model: selectedModel.value,
            }),
        });

        const data = await response.json();
        if (data.success) {
            messages.value = data.messages;
            isConversationStarted.value = true;

            if (data.title_updated) {
                const conversation = conversations.value.find(
                    (c) => c.id === activeConversationId.value
                );
                if (conversation) {
                    conversation.title = data.new_title;
                }
            }

            conversations.value.sort(
                (a, b) => new Date(b.updated_at) - new Date(a.updated_at)
            );
        } else {
            error.value = data.error || 'Une erreur est survenue';
        }
    } catch (err) {
        error.value = 'Une erreur est survenue lors de l\'envoi du message';
        console.error('Error sending message:', err);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="flex h-screen bg-slate-900">
        <ConversationList
            :conversations="conversations"
            :active-conversation-id="activeConversationId"
            @select="selectConversation"
            @new="createNewConversation"
        />

        <div class="flex-1 flex flex-col">
            <ChatHeader
                :conversation-title="conversationTitle"
                :selected-model="selectedModel"
                :models="models"
                :model-disabled="isConversationStarted"
                @update:selected-model="selectedModel = $event"
            />

            <div v-if="error" class="px-4 py-3 bg-red-900/30 border border-red-500/50 text-red-300 text-sm mx-4 mt-4 rounded">
                {{ error }}
            </div>

            <MessageList :messages="messages" :loading="loading" />

            <MessageInput
                :disabled="!activeConversationId || loading"
                @submit="handleMessageSubmit"
            />
        </div>
    </div>
</template>
