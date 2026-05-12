<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { inject } from 'vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

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

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-slate-400">
            Avant de continuer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer. Si vous n'avez pas reçu l'email, nous serons heureux de vous en envoyer un autre.
        </div>

        <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-400">
            Un nouveau lien de vérification a été envoyé à l'adresse email que vous avez fournie dans vos paramètres de profil.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Renvoyer l'Email de Vérification
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm text-blue-400 hover:text-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-0 focus:ring-blue-500 transition"
                >
                    Déconnexion
                </Link>
            </div>
        </form>
    </AuthenticationCard>
</template>
