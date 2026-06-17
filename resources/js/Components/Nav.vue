<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import DropdownSeparator from '@/Components/DropdownSeparator.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();
const showingNavigationDropdown = ref(false);

// Fermer le menu mobile quand la route change
watch(() => page.url, () => {
    showingNavigationDropdown.value = false;
});

const isAuthenticated = computed(() => !!page.props.auth.user);

const logout = () => {
    router.post(route('logout'));
};

const homeRoute = computed(() => isAuthenticated.value ? 'dashboard' : 'welcome');
</script>

<template>
    <nav class="sticky top-0 z-50 bg-background dark:bg-background/80 dark:backdrop-blur-md border-b border-border/50 dark:border-border transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <Link :href="route(homeRoute)" class="flex items-center space-x-2 hover:opacity-80 transition">
                        <span class="text-2xl font-bold text-foreground">
                            🌶️ SaveurIA
                        </span>
                    </Link>
                </div>

                <!-- Desktop Navigation Links - Authenticated -->
                <div v-if="isAuthenticated" class="hidden sm:flex items-center space-x-8">
                    <Link :href="route('ask')" class="text-foreground hover:text-primary dark:hover:text-orange-300 transition font-medium">
                        Parler
                    </Link>
                    <Link :href="route('chat')" class="text-foreground hover:text-primary dark:hover:text-orange-300 transition font-medium">
                        Conversations
                    </Link>
                </div>

                <!-- Desktop Navigation Links - Non-Authenticated -->
                <div v-else class="hidden sm:flex items-center space-x-8">
                    <Link :href="route('login')" class="text-foreground hover:text-primary dark:hover:text-orange-300 transition font-medium">
                        Connexion
                    </Link>
                    <Link :href="route('register')" class="text-foreground hover:text-primary dark:hover:text-orange-300 transition font-medium">
                        Inscription
                    </Link>
                </div>

                <!-- Theme Toggle + Desktop Settings Dropdown - Authenticated -->
                <div v-if="isAuthenticated" class="hidden sm:flex items-center gap-4">
                    <ThemeToggle />
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-secondary transition">
                                <img
                                    v-if="page.props.auth.user.profile_photo_url"
                                    :src="page.props.auth.user.profile_photo_url"
                                    :alt="page.props.auth.user.name"
                                    class="size-8 rounded-full object-cover"
                                />
                                <div v-else class="size-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground font-semibold text-sm">
                                    {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-foreground">{{ page.props.auth.user.name }}</span>
                                    <svg class="-me-0.5 size-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </button>
                        </template>

                        <template #content>
                            <div class="block px-4 py-2 text-xs font-medium text-popover-foreground">
                                Gérer le compte
                            </div>

                            <DropdownLink :href="route('profile.show')">
                                Profil
                            </DropdownLink>

                            <DropdownLink :href="route('settings')">
                                Paramètres
                            </DropdownLink>

                            <DropdownSeparator />

                            <DropdownLink as="button" @action="logout">
                                Déconnexion
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>

                <!-- Theme Toggle - Non-Authenticated -->
                <div v-else class="hidden sm:flex items-center">
                    <ThemeToggle />
                </div>

                <!-- Mobile Hamburger -->
                <div class="flex items-center sm:hidden gap-2">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-secondary focus:outline-none transition">
                        <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu - Authenticated -->
        <div v-if="isAuthenticated" :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-secondary border-t border-border">
            <!-- User Profile Header -->
            <div class="px-4 py-3 border-b border-border flex items-center gap-3">
                <img
                    v-if="page.props.auth.user.profile_photo_url"
                    :src="page.props.auth.user.profile_photo_url"
                    :alt="page.props.auth.user.name"
                    class="size-10 rounded-full object-cover"
                />
                <div v-else class="size-10 rounded-full bg-primary flex items-center justify-center text-primary-foreground font-semibold">
                    {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                </div>
                <span class="text-sm font-medium text-foreground">{{ page.props.auth.user.name }}</span>
            </div>

            <div class="px-2 pt-2 pb-3 space-y-1">
                <Link :href="route('ask')" class="block px-3 py-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-card transition">
                    Parler
                </Link>
                <Link :href="route('chat')" class="block px-3 py-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-card transition">
                    Conversations
                </Link>
            </div>
            <div class="border-t border-border px-2 pt-2 pb-3 space-y-1">
                <Link :href="route('profile.show')" class="block px-3 py-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-card transition">
                    Profil
                </Link>
                <Link :href="route('settings')" class="block px-3 py-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-card transition">
                    Paramètres
                </Link>
                <form @submit.prevent="logout">
                    <button type="button" class="w-full text-left px-3 py-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-card transition">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Navigation Menu - Non-Authenticated -->
        <div v-else :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-secondary border-t border-border">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <Link :href="route('login')" class="block px-3 py-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-card transition">
                    Connexion
                </Link>
                <Link :href="route('register')" class="block px-3 py-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-card transition">
                    Inscription
                </Link>
            </div>
        </div>
    </nav>
</template>
