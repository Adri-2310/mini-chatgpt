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

const models = [
    { id: 'openai/gpt-4o-mini', name: 'GPT-4o mini' },
    { id: 'google/gemini-2.5-flash-exp', name: 'Gemini 2.5 Flash' },
    { id: 'anthropic/claude-3.5-sonnet', name: 'Claude 3.5 Sonnet' },
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
    } catch (error) {
        console.error('Error creating conversation:', error);
    }
};

const selectConversation = async (conversationId) => {
    try {
        activeConversationId.value = conversationId;
        const response = await axios.get(`/conversations/${conversationId}`);
        messages.value = response.data.messages || [];
    } catch (error) {
        console.error('Error loading conversation:', error);
    }
};

const handleMessageSubmit = async (content) => {
    if (!activeConversationId.value) {
        return;
    }

    loading.value = true;
    try {
        const response = await axios.post(`/conversations/${activeConversationId.value}/messages`, {
            content,
            model: selectedModel.value,
        });

        if (response.data.success) {
            messages.value = response.data.messages;

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
        }
    } catch (error) {
        console.error('Error sending message:', error);
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
                @update:selected-model="selectedModel = $event"
            />

            <MessageList :messages="messages" :loading="loading" />

            <MessageInput
                :disabled="!activeConversationId || loading"
                @submit="handleMessageSubmit"
            />
        </div>
    </div>
</template>
