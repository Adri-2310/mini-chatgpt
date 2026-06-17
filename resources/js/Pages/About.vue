<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import Nav from '@/Components/Nav.vue';
</script>

<template>
    <Head title="À propos de SaveurIA" />

    <Nav />

    <div class="min-h-screen bg-background text-foreground">
        <!-- Hero Section -->
        <div class="bg-card/50 border-b border-border">
            <div class="max-w-4xl mx-auto px-4 py-16 sm:py-24">
                <div class="text-center">
                    <span class="text-5xl mb-6 block">🌶️</span>
                    <h1 class="text-4xl sm:text-5xl font-bold mb-4">À propos de SaveurIA</h1>
                    <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                        Un assistant culinaire intelligent alimenté par l'IA pour transformer votre expérience culinaire.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 py-16">
            <!-- Description -->
            <section class="mb-16">
                <h2 class="text-3xl font-bold mb-6">Qu'est-ce que SaveurIA ?</h2>
                <p class="text-lg text-muted-foreground leading-relaxed mb-4">
                    SaveurIA est une application web innovante qui combine la puissance de l'intelligence artificielle avec la passion pour la cuisine.
                    Elle vous permet d'interagir avec trois modèles d'IA différents pour obtenir des inspirations culinaires, des recettes personnalisées,
                    des conseils de cuisine et des suggestions de menus adaptés à vos goûts et vos régimes alimentaires.
                </p>
                <p class="text-lg text-muted-foreground leading-relaxed">
                    Que vous soyez un chef expérimenté ou un débutant en cuisine, SaveurIA est votre compagnon idéal pour explorer de nouvelles saveurs
                    et améliorer vos compétences culinaires.
                </p>
            </section>

            <!-- Fonctionnalités -->
            <section class="mb-16">
                <h2 class="text-3xl font-bold mb-8">Fonctionnalités Principales</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-card border border-border rounded-lg p-6">
                        <div class="text-3xl mb-4">🤖</div>
                        <h3 class="text-xl font-semibold mb-3">Trois Modèles IA</h3>
                        <p class="text-muted-foreground">
                            Accédez à GPT-4o, Gemini 2.5 Flash et Claude 3.5 Haiku pour bénéficier de perspectives culinaires variées et complémentaires.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-card border border-border rounded-lg p-6">
                        <div class="text-3xl mb-4">💬</div>
                        <h3 class="text-xl font-semibold mb-3">Conversations en Temps Réel</h3>
                        <p class="text-muted-foreground">
                            Engagez des conversations fluides avec l'IA pour des conseils culinaires personnalisés et des réponses instantanées.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-card border border-border rounded-lg p-6">
                        <div class="text-3xl mb-4">📚</div>
                        <h3 class="text-xl font-semibold mb-3">Historique des Conversations</h3>
                        <p class="text-muted-foreground">
                            Conservez toutes vos conversations précédentes et accédez à vos recettes favorites et conseils à tout moment.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-card border border-border rounded-lg p-6">
                        <div class="text-3xl mb-4">⚡</div>
                        <h3 class="text-xl font-semibold mb-3">Rapide et Efficace</h3>
                        <p class="text-muted-foreground">
                            Obtenez des réponses instantanées avec un streaming en temps réel pour une expérience fluide et responsive.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-card border border-border rounded-lg p-6">
                        <div class="text-3xl mb-4">🔐</div>
                        <h3 class="text-xl font-semibold mb-3">Sécurité et Confidentialité</h3>
                        <p class="text-muted-foreground">
                            Vos données sont protégées avec une authentification sécurisée et une double authentification optionnelle.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-card border border-border rounded-lg p-6">
                        <div class="text-3xl mb-4">🌙</div>
                        <h3 class="text-xl font-semibold mb-3">Mode Clair/Sombre</h3>
                        <p class="text-muted-foreground">
                            Personnalisez votre expérience avec un thème adapté à vos préférences et à votre environnement.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Crédits -->
            <section class="bg-card border border-border rounded-lg p-8">
                <h2 class="text-2xl font-bold mb-6">À propos du Projet</h2>
                <div class="space-y-4 text-muted-foreground">
                    <p>
                        <span class="font-semibold text-foreground">Auteur :</span> Adrien Mertens
                    </p>
                    <p>
                        <span class="font-semibold text-foreground">Projet :</span> Projet de développement SGBD
                    </p>
                    <p class="text-sm pt-4 border-t border-border/50">
                        SaveurIA a été créé comme projet académique pour démontrer les capacités du développement web moderne
                        en combinant une architecture backend robuste avec une interface frontend intuitive et réactive.
                    </p>
                </div>
            </section>

            <!-- CTA -->
            <div class="text-center mt-16">
                <p class="text-lg text-muted-foreground mb-6">Prêt à commencer votre aventure culinaire ?</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link :href="route('register')" class="inline-block px-8 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition font-semibold">
                        Créer un compte
                    </Link>
                    <Link :href="route('welcome')" class="inline-block px-8 py-3 border-2 border-primary text-primary hover:bg-primary hover:text-primary-foreground rounded-lg transition font-semibold">
                        Retour à l'accueil
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
