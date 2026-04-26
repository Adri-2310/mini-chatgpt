<script setup>
import { Head, usePage, Link } from '@inertiajs/vue3';
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
    <div>
        <!-- Hero Section -->
        <div class="mb-16 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">
                Bienvenue, {{ $page.props.auth.user.name }}!
            </h1>
            <p class="text-slate-300 text-lg max-w-2xl mx-auto">
                Explorez les capacités de l'IA avec notre assistant polyvalent. Posez une question ou lancez une conversation.
            </p>
        </div>

        <!-- Feature Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Parler Card -->
            <Link :href="route('ask')" class="group">
                <div class="h-full p-8 rounded-lg border-2 border-blue-400 bg-slate-800/50 hover:bg-slate-800 transition transform hover:scale-105">
                    <div class="text-4xl mb-4">📝</div>
                    <h2 class="text-xl font-bold text-white mb-2">Parler</h2>
                    <p class="text-slate-300 mb-4">
                        Posez une question à l'IA et obtenez une réponse instantanée.
                    </p>
                    <div class="inline-flex items-center text-blue-400 font-medium group-hover:translate-x-2 transition">
                        Commencer →
                    </div>
                </div>
            </Link>

            <!-- Conversations Card -->
            <Link :href="route('chat')" class="group">
                <div class="h-full p-8 rounded-lg border-2 border-purple-400 bg-slate-800/50 hover:bg-slate-800 transition transform hover:scale-105">
                    <div class="text-4xl mb-4">💬</div>
                    <h2 class="text-xl font-bold text-white mb-2">Conversations</h2>
                    <p class="text-slate-300 mb-4">
                        Maintenez des conversations multi-tours avec historique persistant.
                    </p>
                    <div class="inline-flex items-center text-purple-400 font-medium group-hover:translate-x-2 transition">
                        Explorer →
                    </div>
                </div>
            </Link>

            <!-- Paramètres Card -->
            <Link :href="route('settings')" class="group">
                <div class="h-full p-8 rounded-lg border-2 border-green-400 bg-slate-800/50 hover:bg-slate-800 transition transform hover:scale-105">
                    <div class="text-4xl mb-4">⚙️</div>
                    <h2 class="text-xl font-bold text-white mb-2">Paramètres</h2>
                    <p class="text-slate-300 mb-4">
                        Personnalisez vos instructions et préférences d'IA.
                    </p>
                    <div class="inline-flex items-center text-green-400 font-medium group-hover:translate-x-2 transition">
                        Configurer →
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>
