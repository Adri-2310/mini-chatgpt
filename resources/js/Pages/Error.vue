<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

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
    <div class="min-h-screen bg-background flex items-center justify-center px-4 transition-colors">
        <Head :title="`${props.status} - ${error.title}`" />
            <div class="text-center max-w-md">
                <div class="text-6xl mb-6">{{ error.emoji }}</div>

                <div class="text-7xl font-bold text-primary mb-4">
                    {{ props.status }}
                </div>

                <h1 class="text-3xl font-bold text-foreground mb-3">
                    {{ error.title }}
                </h1>

                <p class="text-muted-foreground text-lg mb-8">
                    {{ error.description }}
                </p>

                <div>
                    <Link
                        :href="route(error.link)"
                        class="inline-block px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition font-semibold"
                    >
                        Retour à l'accueil
                    </Link>
                </div>
            </div>
    </div>
</template>
