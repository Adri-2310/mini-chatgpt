<script setup>
import { ref, watch, nextTick } from 'vue';
import ChatMessage from './ChatMessage.vue';
import LoadingIndicator from './LoadingIndicator.vue';

const props = defineProps({
    messages: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const messageContainer = ref(null);

const scrollToBottom = async () => {
    await nextTick();
    if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
};

watch(
    () => [props.messages.length, props.loading],
    () => scrollToBottom(),
    { flush: 'post' }
);
</script>

<template>
    <div
        ref="messageContainer"
        class="flex-1 overflow-y-auto p-4 space-y-4"
        :style="{ scrollBehavior: 'smooth' }"
    >
        <template v-if="messages.length === 0">
            <div class="h-full flex items-center justify-center text-muted-foreground">
                <p>Aucun message. Commencez une conversation!</p>
            </div>
        </template>

        <template v-else>
            <div
                v-for="message in messages"
                :key="message.id"
                class="animate-fade-in"
            >
                <ChatMessage
                    :role="message.role"
                    :content="message.content"
                    :tokens-used="message.tokens_used"
                />
            </div>
        </template>

        <LoadingIndicator v-if="loading" />
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>
