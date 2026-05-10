<script setup>
import { usePage } from '@inertiajs/vue3';
import { watch, onMounted, inject } from 'vue';

const page = usePage();
const $toastr = inject('$toastr');

const props = defineProps({
    status: String,
});

const messageTranslations = {
    'profile-information-updated': 'Vos informations de profil ont été mises à jour avec succès.',
    'password-updated': 'Votre mot de passe a été mis à jour avec succès.',
    'two-factor-authentication-enabled': 'L\'authentification à deux facteurs a été activée.',
    'two-factor-authentication-disabled': 'L\'authentification à deux facteurs a été désactivée.',
};

const translateMessage = (message) => {
    return messageTranslations[message] || message;
};

const showToast = (message, type = 'success') => {
    if (message && $toastr) {
        const translatedMessage = translateMessage(message);
        $toastr[type](translatedMessage);
    }
};

onMounted(() => {
    if (props.status) {
        showToast(props.status, 'success');
    }
    if (page.props.flash?.status) {
        showToast(page.props.flash.status, 'success');
    }
    if (page.props.flash?.error) {
        showToast(page.props.flash.error, 'error');
    }
});

watch(
    () => page.props.flash?.status,
    (newStatus) => {
        if (newStatus) {
            showToast(newStatus, 'success');
        }
    }
);

watch(
    () => page.props.flash?.error,
    (newError) => {
        if (newError) {
            showToast(newError, 'error');
        }
    }
);
</script>

<template>
    <!-- Toastr notifications handled globally -->
</template>
