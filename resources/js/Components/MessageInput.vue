<script setup>
import { ref } from 'vue';
import PrimaryButton from './PrimaryButton.vue';

defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit']);

const content = ref('');
const textarea = ref(null);
const showEmojiPicker = ref(false);

const emojis = ['👍', '👎', '😊', '😂', '🤔', '❤️', '🎉', '🚀', '✨', '🔥', '😍', '😭'];

const handleSubmit = () => {
    if (content.value.trim()) {
        emit('submit', content.value);
        content.value = '';
        if (textarea.value) {
            textarea.value.style.height = 'auto';
        }
    }
};

const handleInput = () => {
    if (textarea.value) {
        textarea.value.style.height = 'auto';
        textarea.value.style.height = Math.min(textarea.value.scrollHeight, 200) + 'px';
    }
};

const insertEmoji = (emoji) => {
    content.value += emoji;
    showEmojiPicker.value = false;
    if (textarea.value) {
        textarea.value.focus();
        handleInput();
    }
};
</script>

<template>
    <div class="border-t border-border bg-card/50 p-4 transition-colors">
        <form @submit.prevent="handleSubmit" class="flex gap-3 items-end max-w-4xl mx-auto">
            <!-- Emoji Button -->
            <div class="relative">
                <button
                    type="button"
                    @click="showEmojiPicker = !showEmojiPicker"
                    :disabled="disabled"
                    class="p-3 hover:bg-secondary rounded-lg transition text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Ajouter un emoji"
                >
                    😊
                </button>

                <!-- Emoji Picker -->
                <Transition
                    enter-active-class="animate-fade-in-up"
                    leave-active-class="animate-fade-out-down"
                >
                    <div
                        v-if="showEmojiPicker"
                        class="absolute bottom-full left-0 mb-2 bg-card border border-border rounded-lg shadow-lg p-3 grid grid-cols-4 gap-2 w-48 z-50"
                    >
                        <button
                            v-for="emoji in emojis"
                            :key="emoji"
                            type="button"
                            @click="insertEmoji(emoji)"
                            class="text-xl hover:bg-secondary rounded p-2 transition text-center"
                        >
                            {{ emoji }}
                        </button>
                    </div>
                </Transition>
            </div>

            <textarea
                ref="textarea"
                v-model="content"
                placeholder="Écrivez votre message..."
                :disabled="disabled"
                @input="handleInput"
                rows="1"
                class="flex-1 px-4 py-3 bg-input border border-border text-foreground placeholder-muted-foreground rounded-lg focus:border-primary focus:ring-2 focus:ring-primary focus:ring-offset-0 transition disabled:opacity-50 disabled:cursor-not-allowed resize-none max-h-48"
            ></textarea>

            <PrimaryButton
                type="submit"
                :disabled="disabled || !content.trim()"
            >
                {{ disabled ? 'Envoi...' : 'Envoyer' }}
            </PrimaryButton>
        </form>
    </div>
</template>

<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeOutDown {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(8px);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.2s ease-out;
}

.animate-fade-out-down {
    animation: fadeOutDown 0.2s ease-in;
}
</style>
