<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
  layout: AppLayout,
  layoutProps: {
    title: 'Dashboard'
  }
});
</script>

<template>
    <Head title="Dashboard" />

    <!-- Hero Section -->
    <div class="mb-16 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold text-foreground mb-4">
            Bienvenue sur SaveurIA, {{ $page.props.auth.user.name }}!
        </h1>
        <p class="text-muted-foreground text-lg max-w-2xl mx-auto">
            Besoin d'inspiration culinaire? Cherchez une recette? SaveurIA est là pour transformer vos idées en délicieux repas!
        </p>
    </div>

    <!-- Feature Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Parler Card -->
        <Link :href="route('ask')" class="group">
            <div class="h-full p-8 rounded-lg border-2 border-primary bg-card hover:bg-secondary transition transform hover:scale-105">
                <div class="text-4xl mb-4">👨‍🍳</div>
                <h2 class="text-xl font-bold text-foreground mb-2">Recette Rapide</h2>
                <p class="text-muted-foreground mb-4">
                    Posez votre question culinaire et obtenez une recette ou conseil instantanément.
                </p>
                <div class="inline-flex items-center text-primary font-medium group-hover:translate-x-2 transition">
                    Commencer →
                </div>
            </div>
        </Link>

        <!-- Conversations Card -->
        <Link :href="route('chat')" class="group">
            <div class="h-full p-8 rounded-lg border-2 border-primary bg-card hover:bg-secondary transition transform hover:scale-105">
                <div class="text-4xl mb-4">📚</div>
                <h2 class="text-xl font-bold text-foreground mb-2">Mes Recettes</h2>
                <p class="text-muted-foreground mb-4">
                    Continuez vos explorations culinaires avec l'historique de vos conversations.
                </p>
                <div class="inline-flex items-center text-primary font-medium group-hover:translate-x-2 transition">
                    Explorer →
                </div>
            </div>
        </Link>

        <!-- Paramètres Card -->
        <Link :href="route('settings')" class="group">
            <div class="h-full p-8 rounded-lg border-2 border-primary bg-card hover:bg-secondary transition transform hover:scale-105">
                <div class="text-4xl mb-4">🌶️</div>
                <h2 class="text-xl font-bold text-foreground mb-2">Personnaliser SaveurIA</h2>
                <p class="text-muted-foreground mb-4">
                    Définissez vos préférences culinaires, allergies et style de cuisine.
                </p>
                <div class="inline-flex items-center text-primary font-medium group-hover:translate-x-2 transition">
                    Configurer →
                </div>
            </div>
        </Link>
    </div>
</template>
