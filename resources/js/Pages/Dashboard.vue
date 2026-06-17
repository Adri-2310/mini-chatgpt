<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
  layout: AppLayout,
  layoutProps: {
    title: 'Dashboard'
  }
});

defineProps({
  stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <!-- Hero Section -->
    <div class="mb-12 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold text-foreground mb-4">
            Bienvenue sur SaveurIA, {{ $page.props.auth.user.name }}!
        </h1>
        <p class="text-muted-foreground text-lg max-w-2xl mx-auto">
            Besoin d'inspiration culinaire? Cherchez une recette? SaveurIA est là pour transformer vos idées en délicieux repas!
        </p>
    </div>

    <!-- Statistics Section -->
    <div class="mb-16" v-if="stats">
        <h2 class="text-2xl font-bold text-foreground mb-6">📊 Vos Statistiques</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Monthly Cost Card -->
            <div class="p-6 rounded-lg border border-border bg-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted-foreground text-sm mb-2">Coût ce Mois</p>
                        <p class="text-3xl font-bold text-foreground">${{ stats.monthly_cost.toFixed(6) }}</p>
                        <p class="text-xs text-muted-foreground mt-2">depuis le 1er du mois</p>
                    </div>
                    <div class="text-4xl">💳</div>
                </div>
            </div>

            <!-- Total Conversations Card -->
            <div class="p-6 rounded-lg border border-border bg-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted-foreground text-sm mb-2">Conversations</p>
                        <p class="text-3xl font-bold text-foreground">{{ stats.conversations_total }}</p>
                        <p class="text-xs text-muted-foreground mt-2">total</p>
                    </div>
                    <div class="text-4xl">📚</div>
                </div>
            </div>

            <!-- Monthly Messages Card -->
            <div class="p-6 rounded-lg border border-border bg-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted-foreground text-sm mb-2">Messages ce Mois</p>
                        <p class="text-3xl font-bold text-foreground">{{ stats.monthly_messages }}</p>
                        <p class="text-xs text-muted-foreground mt-2">depuis le 1er du mois</p>
                    </div>
                    <div class="text-4xl">💬</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Section -->
    <div>
        <h2 class="text-2xl font-bold text-foreground mb-6">🚀 Actions Rapides</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
    </div>
</template>
