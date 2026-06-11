<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { inject } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const props = defineProps({
    status: String,
});

const page = usePage();
const $toastr = inject('$toastr');

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'), {
        onSuccess: () => {
            if ($toastr) {
                $toastr.success('Un nouveau lien de vérification a été envoyé à votre adresse email.');
            }
        },
        onError: () => {
            if ($toastr) {
                $toastr.error('Une erreur est survenue lors de l\'envoi du lien de vérification.');
            }
        },
    });
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Vérification de l'Email" />

    <ToastNotification />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-background transition-colors">
        <div class="absolute top-4 right-4">
            <ThemeToggle />
        </div>

        <div class="mb-8">
            <span class="text-2xl font-bold text-foreground">
                🌶️ SaveurIA
            </span>
        </div>

        <div class="w-full sm:max-w-md px-8 py-8 bg-card border border-border rounded-xl shadow-lg">
            <h1 class="text-2xl font-bold text-foreground mb-2">Vérification de l'Email</h1>
            <p class="text-muted-foreground text-sm mb-6">Avant de continuer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.</p>

            <div v-if="verificationLinkSent" class="mb-4 p-4 bg-green-100 dark:bg-green-950 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    Un nouveau lien de vérification a été envoyé à votre adresse email.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <PrimaryButton type="submit" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Envoi...' : 'Renvoyer l\'Email de Vérification' }}
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full text-sm text-primary hover:underline text-center transition"
                >
                    Déconnexion
                </Link>
            </form>
        </div>
    </div>
</template>
