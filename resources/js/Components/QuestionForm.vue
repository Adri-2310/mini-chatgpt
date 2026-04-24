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

const question = ref('');

const handleSubmit = () => {
    if (question.value.trim()) {
        emit('submit', question.value);
        question.value = '';
    }
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
            <label for="question" class="block text-sm font-medium text-slate-300 mb-2">
                Votre question
            </label>
            <textarea
                id="question"
                v-model="question"
                placeholder="Posez votre question ici..."
                :disabled="disabled"
                rows="4"
                class="w-full px-4 py-3 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 transition disabled:opacity-50 disabled:cursor-not-allowed"
            ></textarea>
        </div>

        <div class="flex justify-end">
            <PrimaryButton
                type="submit"
                :disabled="disabled || !question.trim()"
                class="w-full sm:w-auto"
            >
                {{ disabled ? 'Chargement...' : 'Poser la question' }}
            </PrimaryButton>
        </div>
    </form>
</template>
