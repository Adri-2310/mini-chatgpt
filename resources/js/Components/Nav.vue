<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const showingNavigationDropdown = ref(false);

const isAuthenticated = computed(() => !!page.props.auth.user);

const logout = () => {
    router.post(route('logout'));
};

const homeRoute = computed(() => isAuthenticated.value ? 'dashboard' : 'welcome');
</script>

<template>
    <nav class="sticky top-0 z-50 bg-slate-900/80 backdrop-blur-md border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <Link :href="route(homeRoute)" class="flex items-center space-x-2 hover:opacity-80 transition">
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                            🤖 Mini-ChatGPT
                        </span>
                    </Link>
                </div>

                <!-- Desktop Navigation Links - Authenticated -->
                <div v-if="isAuthenticated" class="hidden sm:flex items-center space-x-8">
                    <Link :href="route('ask')" class="text-slate-300 hover:text-white transition font-medium">
                        Parler
                    </Link>
                    <Link :href="route('chat')" class="text-slate-300 hover:text-white transition font-medium">
                        Conversations
                    </Link>
                </div>

                <!-- Desktop Navigation Links - Non-Authenticated -->
                <div v-else class="hidden sm:flex items-center space-x-8">
                    <Link :href="route('login')" class="text-slate-300 hover:text-white transition font-medium">
                        Connexion
                    </Link>
                    <Link :href="route('register')" class="text-slate-300 hover:text-white transition font-medium">
                        Inscription
                    </Link>
                </div>

                <!-- Desktop Settings Dropdown - Authenticated -->
                <div v-if="isAuthenticated" class="hidden sm:flex items-center">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-700 transition">
                                <img
                                    v-if="page.props.auth.user.profile_photo_url"
                                    :src="page.props.auth.user.profile_photo_url"
                                    :alt="page.props.auth.user.name"
                                    class="size-8 rounded-full object-cover"
                                />
                                <div v-else class="size-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-slate-300">{{ page.props.auth.user.name }}</span>
                                    <svg class="-me-0.5 size-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </button>
                        </template>

                        <template #content>
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                Gérer le compte
                            </div>

                            <DropdownLink :href="route('profile.show')">
                                Profil
                            </DropdownLink>

                            <DropdownLink :href="route('settings')">
                                Paramètres
                            </DropdownLink>

                            <div class="border-t border-gray-200" />

                            <form @submit.prevent="logout">
                                <DropdownLink as="button">
                                    Déconnexion
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

        <!-- Mobile Navigation Menu - Authenticated -->
        <div v-if="isAuthenticated" :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-slate-800/50 border-t border-slate-700">
            <!-- User Profile Header -->
            <div class="px-4 py-3 border-b border-slate-700 flex items-center gap-3">
                <img
                    v-if="page.props.auth.user.profile_photo_url"
                    :src="page.props.auth.user.profile_photo_url"
                    :alt="page.props.auth.user.name"
                    class="size-10 rounded-full object-cover"
                />
                <div v-else class="size-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold">
                    {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                </div>
                <span class="text-sm font-medium text-white">{{ page.props.auth.user.name }}</span>
            </div>

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
                    Profil
                </Link>
                <Link :href="route('settings')" class="block px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                    Paramètres
                </Link>
                <form @submit.prevent="logout">
                    <button type="button" class="w-full text-left px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Navigation Menu - Non-Authenticated -->
        <div v-else :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-slate-800/50 border-t border-slate-700">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <Link :href="route('login')" class="block px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                    Connexion
                </Link>
                <Link :href="route('register')" class="block px-3 py-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-700 transition">
                    Inscription
                </Link>
            </div>
        </div>
    </nav>
</template>
