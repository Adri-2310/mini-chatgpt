<script setup>
import { ref, inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const $toastr = inject('$toastr');

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            if ($toastr) {
                $toastr.success('Votre compte a été supprimé avec succès.');
            }
        },
        onError: () => {
            passwordInput.value.focus();
            if ($toastr && Object.keys(form.errors).length > 0) {
                Object.values(form.errors).forEach(error => {
                    if (error) {
                        $toastr.error(error);
                    }
                });
            }
        },
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Supprimer le Compte
        </template>

        <template #description>
            Supprimez définitivement votre compte.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-slate-400">
                Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, téléchargez les données ou informations que vous souhaitez conserver.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmUserDeletion">
                    Supprimer le Compte
                </DangerButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    Supprimer le Compte
                </template>

                <template #content>
                    Êtes-vous sûr de vouloir supprimer votre compte ? Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Veuillez entrer votre mot de passe pour confirmer que vous souhaitez définitivement supprimer votre compte.

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Mot de passe"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Annuler
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Supprimer le Compte
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
