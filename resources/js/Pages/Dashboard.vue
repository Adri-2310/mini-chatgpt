<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head title="Dashboard" />

        <div class="min-h-screen bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900">
            <!-- Navigation -->
            <nav class="sticky top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <Link :href="route('dashboard')" class="flex items-center space-x-2 hover:opacity-80 transition">
                                <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                                    🤖 Mini-ChatGPT
                                </span>
                            </Link>
                        </div>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden sm:flex items-center space-x-8">
                            <Link :href="route('ask')" class="text-slate-300 hover:text-white transition font-medium">
                                Parler
                            </Link>
                            <Link :href="route('chat')" class="text-slate-300 hover:text-white transition font-medium">
                                Conversations
                            </Link>
                        </div>

                        <!-- Desktop Settings Dropdown -->
                        <div class="hidden sm:flex items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center text-sm font-medium text-slate-300 hover:text-white transition">
                                        {{ $page.props.auth.user.name }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Account
                                    </div>

                                    <DropdownLink :href="route('profile.show')">
                                        Profile
                                    </DropdownLink>

                                    <DropdownLink :href="route('settings')">
                                        Settings
                                    </DropdownLink>

                                    <div class="border-t border-gray-200" />

                                    <form @submit.prevent="logout">
                                        <DropdownLink as="button">
                                            Log Out
                                        </DropdownLink>
                                    </form>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Mobile Hamburger -->
                        <div class="flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-300 hover:bg-slate-800 focus:outline-none transition">
                                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-slate-800/50 border-t border-slate-700">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <Link :href="route('ask')" class="block px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                            Parler
                        </Link>
                        <Link :href="route('chat')" class="block px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                            Conversations
                        </Link>
                    </div>
                    <div class="border-t border-slate-700 px-2 pt-2 pb-3 space-y-1">
                        <Link :href="route('profile.show')" class="block px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                            Profile
                        </Link>
                        <Link :href="route('settings')" class="block px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                            Settings
                        </Link>
                        <form @submit.prevent="logout">
                            <button type="button" class="w-full text-left px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
            </main>
        </div>
    </div>
</template>
