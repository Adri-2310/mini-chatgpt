<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useStream } from '@laravel/stream-vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ModelSelector from '@/Components/ModelSelector.vue';
import QuestionForm from '@/Components/QuestionForm.vue';
import ResponseDisplay from '@/Components/ResponseDisplay.vue';

defineOptions({
  layout: AppLayout,
  layoutProps: {
    title: 'Poser une question'
  }
});

const props = defineProps({
    models: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const selectedModel = ref('anthropic/claude-3.5-haiku');
const response = ref('');
const error = ref('');

// Création d'un buffer
let streamBuffer = '';

const { isStreaming, send: sendStream } = useStream('/ask/stream', {
    onData: (rawData) => {
        streamBuffer += rawData;
        const lines = streamBuffer.split('\n');
        streamBuffer = lines.pop() || '';

        for (const line of lines) {
            if (line.trim() === '') continue;

            if (line.includes('[DONE]')) {
                return;
            }

            if (line.startsWith('data: ')) {
                const jsonStr = line.substring(6);
                try {
                    const data = JSON.parse(jsonStr);
                    if (data.content) {
                        response.value += data.content;
                    }
                } catch (e) {
                    console.error('❌ Erreur de parsing JSON :', e, "Texte qui a posé problème :", jsonStr);
                }
            }
        }
    },
    onFinish: () => {
        streamBuffer = '';
    },
    onError: (err) => {
        error.value = 'Erreur lors du streaming: ' + err;
        console.error('🔴 Stream error:', err);
    },
});

const handleSubmit = async (question) => {
    if (!selectedModel.value) {
        error.value = 'Veuillez sélectionner un modèle';
        return;
    }

    error.value = '';
    response.value = '';
    streamBuffer = ''; // Réinitialisation du buffer pour la nouvelle question

    sendStream({
        question: question,
        model: selectedModel.value,
    });
};
</script>

<template>
    <Head title="Poser une question" />

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-lg rounded-lg p-6 sm:p-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Discutez avec les meilleures IA du marché
                    </h1>
                    <p class="text-slate-400">
                        Posez une question unique sans historique. Chaque question est traitée indépendamment.
                    </p>
                </div>

                <div
                    v-if="error"
                    class="mb-6 p-4 bg-red-900/30 border border-red-700 rounded-lg text-red-300"
                >
                    {{ error }}
                </div>

                <div class="mb-6">
                    <ModelSelector
                        :models="models"
                        v-model="selectedModel"
                        :disabled="isStreaming"
                    />
                </div>

                <div class="mb-8">
                    <QuestionForm
                        :disabled="isStreaming"
                        @submit="handleSubmit"
                    />
                </div>

                <ResponseDisplay :content="response" />
            </div>
        </div>
</template>