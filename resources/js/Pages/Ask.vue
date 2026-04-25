<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ModelSelector from '@/Components/ModelSelector.vue';
import QuestionForm from '@/Components/QuestionForm.vue';
import ResponseDisplay from '@/Components/ResponseDisplay.vue';

defineProps({
    models: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const selectedModel = ref('');
const response = ref('');
const loading = ref(false);
const error = ref('');

const handleSubmit = async (question) => {
    if (!selectedModel.value) {
        error.value = 'Veuillez sélectionner un modèle';
        return;
    }

    loading.value = true;
    error.value = '';
    response.value = '';

    try {
        const result = await fetch(route('ask.store'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token,
            },
            body: JSON.stringify({
                question: question,
                model: selectedModel.value,
            }),
        });

        const data = await result.json();
        if (data.success) {
            response.value = data.response;
        } else {
            error.value = data.error || 'Une erreur est survenue';
        }
    } catch (err) {
        error.value = 'Erreur lors de l\'appel API';
        console.error('API Error:', err);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="Poser une question" />

    <AppLayout title="Poser une question">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🤖 Posez une question à une IA
            </h2>
        </template>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-lg rounded-lg p-6 sm:p-8">
                <!-- Titre & description -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Discutez avec les meilleures IA du marché
                    </h1>
                    <p class="text-slate-400">
                        Posez une question unique sans historique. Chaque question est traitée indépendamment.
                    </p>
                </div>

                <!-- Message d'erreur -->
                <div
                    v-if="error"
                    class="mb-6 p-4 bg-red-900/30 border border-red-700 rounded-lg text-red-300"
                >
                    {{ error }}
                </div>

                <!-- Sélecteur de modèle -->
                <div class="mb-6">
                    <ModelSelector
                        :models="models"
                        v-model="selectedModel"
                    />
                </div>

                <!-- Formulaire de question -->
                <div class="mb-8">
                    <QuestionForm
                        :disabled="loading"
                        @submit="handleSubmit"
                    />
                </div>

                <!-- Réponse -->
                <ResponseDisplay :content="response" />
            </div>
        </div>
    </AppLayout>
</template>
