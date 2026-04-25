<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import ConversationList from '../Components/ConversationList.vue';
import ChatHeader from '../Components/ChatHeader.vue';
import MessageList from '../Components/MessageList.vue';
import MessageInput from '../Components/MessageInput.vue';

const conversations = ref([]);
const activeConversationId = ref(null);
const messages = ref([]);
const selectedModel = ref('openai/gpt-4o-mini');
const loading = ref(false);
const error = ref(null);
const isConversationStarted = ref(false);

const models = [
    { id: 'openai/gpt-4o-mini', name: 'GPT-4o mini' },
    { id: 'google/gemini-2.5-flash-exp', name: 'Gemini 2.5 Flash' },
    { id: 'anthropic/claude-3.5-haiku', name: 'Claude 3.5 Haiku' },
];

const activeConversation = computed(() => {
    return conversations.value.find((c) => c.id === activeConversationId.value);
});

const conversationTitle = computed(() => {
    return activeConversation.value?.title || 'Nouvelle conversation';
});

const loadConversations = async () => {
    try {
        const response = await axios.get('/conversations');
        conversations.value = response.data;
    } catch (error) {
        console.error('Error loading conversations:', error);
    }
};

const createNewConversation = async () => {
    try {
        const response = await axios.post('/conversations', {});
        conversations.value.unshift(response.data);
        activeConversationId.value = response.data.id;
        messages.value = [];
        isConversationStarted.value = false;
        error.value = null;
    } catch (error) {
        console.error('Error creating conversation:', error);
    }
};

const selectConversation = async (conversationId) => {
    try {
        activeConversationId.value = conversationId;
        const response = await axios.get(`/conversations/${conversationId}`);
        messages.value = response.data.messages || [];
        isConversationStarted.value = (messages.value.length > 0);
        error.value = null;
    } catch (error) {
        console.error('Error loading conversation:', error);
    }
};

const handleMessageSubmit = async (content) => {
    if (!activeConversationId.value) {
        return;
    }

    loading.value = true;
    error.value = null;
    try {
        const response = await axios.post(`/conversations/${activeConversationId.value}/messages`, {
            content,
            model: selectedModel.value,
        });

        if (response.data.success) {
            messages.value = response.data.messages;
            isConversationStarted.value = true;

            if (response.data.title_updated) {
                const conversation = conversations.value.find(
                    (c) => c.id === activeConversationId.value
                );
                if (conversation) {
                    conversation.title = response.data.new_title;
                }
            }

            conversations.value.sort(
                (a, b) => new Date(b.updated_at) - new Date(a.updated_at)
            );
        } else {
            error.value = response.data.error || 'Une erreur est survenue';
        }
    } catch (err) {
        error.value = err.response?.data?.error || 'Une erreur est survenue lors de l\'envoi du message';
        console.error('Error sending message:', err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadConversations();
});
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
