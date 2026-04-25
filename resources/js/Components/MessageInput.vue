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
</script>

<template>
    <div class="border-t border-slate-600 bg-slate-800/50 p-4">
        <form @submit.prevent="handleSubmit" class="space-y-3">
            <textarea
                ref="textarea"
                v-model="content"
                placeholder="Écrivez votre message..."
                :disabled="disabled"
                @input="handleInput"
                rows="1"
                class="w-full px-4 py-3 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 transition disabled:opacity-50 disabled:cursor-not-allowed resize-none max-h-48"
            ></textarea>

            <div class="flex justify-end">
                <PrimaryButton
                    type="submit"
                    :disabled="disabled || !content.trim()"
                >
                    {{ disabled ? 'Envoi...' : 'Envoyer' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
