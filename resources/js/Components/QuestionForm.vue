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

const isValid = () => {
    const trimmed = question.value.trim();
    return trimmed.length >= 5 && trimmed.length <= 2000;
};

const handleSubmit = () => {
    if (isValid()) {
        emit('submit', question.value);
        question.value = '';
    }
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
            <label for="question" class="block text-sm font-medium text-muted-foreground mb-2">
                Votre question
            </label>
            <textarea
                id="question"
                v-model="question"
                placeholder="Posez votre question ici..."
                :disabled="disabled"
                rows="4"
                class="w-full px-4 py-3 bg-input border border-border text-foreground placeholder-muted-foreground rounded-lg focus:border-primary focus:ring-2 focus:ring-primary focus:ring-offset-0 transition disabled:opacity-50 disabled:cursor-not-allowed"
            ></textarea>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div v-if="question.trim() && !isValid()" class="text-sm text-accent-foreground">
                {{ question.trim().length < 5 ? 'Minimum 5 caractères' : 'Maximum 2000 caractères' }}
            </div>
            <div v-else class="text-sm text-muted-foreground">
                {{ question.trim().length }} / 2000
            </div>
            <PrimaryButton
                type="submit"
                :disabled="disabled || !isValid()"
                class="w-full sm:w-auto"
            >
                {{ disabled ? 'Chargement...' : 'Poser la question' }}
            </PrimaryButton>
        </div>
    </form>
</template>
