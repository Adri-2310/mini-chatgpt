<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ErrorNav from '@/Components/ErrorNav.vue';

const props = defineProps({
    status: {
        type: Number,
        required: true
    }
});

const errorDetails = {
    401: {
        emoji: '🔐',
        title: 'Non authentifié',
        description: 'Veuillez vous connecter pour accéder à cette ressource.',
        link: 'login'
    },
    403: {
        emoji: '🚫',
        title: 'Accès refusé',
        description: 'Vous n\'avez pas la permission d\'accéder à cette ressource.',
        link: 'welcome'
    },
    404: {
        emoji: '🔍',
        title: 'Page non trouvée',
        description: 'La page que vous recherchez n\'existe pas ou a été supprimée.',
        link: 'welcome'
    },
    500: {
        emoji: '⚠️',
        title: 'Erreur serveur',
        description: 'Une erreur interne s\'est produite. Nos équipes ont été notifiées.',
        link: 'welcome'
    },
    503: {
        emoji: '🔧',
        title: 'Service indisponible',
        description: 'Le service est actuellement indisponible. Veuillez réessayer plus tard.',
        link: 'welcome'
    }
};

const error = computed(() => errorDetails[props.status] || {
    emoji: '❌',
    title: 'Une erreur s\'est produite',
    description: 'Une erreur inattendue s\'est produite.',
    link: 'welcome'
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 flex flex-col">
        <Head :title="`${props.status} - ${error.title}`" />

        <ErrorNav />

        <div class="flex-1 flex items-center justify-center px-4">
            <div class="text-center max-w-md">
                <div class="text-6xl mb-6">{{ error.emoji }}</div>

                <div class="text-7xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent mb-4">
                    {{ props.status }}
                </div>

                <h1 class="text-3xl font-bold text-white mb-3">
                    {{ error.title }}
                </h1>

                <p class="text-slate-300 text-lg mb-8">
                    {{ error.description }}
                </p>

                <div class="flex gap-4 justify-center">
                    <Link
                        :href="route(error.link)"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-500 hover:to-purple-500 transition font-semibold"
                    >
                        Retour à l'accueil
                    </Link>
                    <Link
                        :href="route('chat')"
                        class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition font-semibold"
                    >
                        Conversations
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
