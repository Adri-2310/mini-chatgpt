<script setup>
import { ref, watch, nextTick, computed } from 'vue';
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

const totalTokens = computed(() =>
    props.messages.reduce((sum, m) => sum + (m.tokens_used || 0), 0)
);

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
            <ChatMessage
                v-for="message in messages"
                :key="message.id"
                :role="message.role"
                :content="message.content"
                :tokens-used="message.tokens_used"
            />
        </template>

        <LoadingIndicator v-if="loading" />

        <div v-if="messages.length > 0 && totalTokens > 0" class="text-xs text-muted-foreground text-center py-2 border-t border-border">
            Total conversation : {{ totalTokens.toLocaleString() }} tokens
        </div>
    </div>
</template>
