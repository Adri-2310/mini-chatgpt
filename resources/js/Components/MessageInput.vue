<script setup>
import { ref } from 'vue';
import PrimaryButton from './PrimaryButton.vue';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/ui/popover';

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
    <div class="border-t border-border bg-card/50 p-2 md:p-4 transition-colors">
        <form @submit.prevent="handleSubmit" class="flex gap-2 md:gap-3 items-end max-w-4xl mx-auto">
            <!-- Emoji Picker with Popover -->
            <Popover v-model:open="showEmojiPicker">
                <PopoverTrigger as-child>
                    <button
                        type="button"
                        :disabled="disabled"
                        class="p-3 hover:bg-secondary rounded-lg transition text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Ajouter un emoji"
                    >
                        😊
                    </button>
                </PopoverTrigger>

                <PopoverContent class="w-48 p-3">
                    <div class="grid grid-cols-4 gap-2">
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
                </PopoverContent>
            </Popover>

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
