<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { inject } from 'vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import PasswordRequirements from '@/Components/PasswordRequirements.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const $toastr = inject('$toastr');

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const passwordRequirementsMet = computed(() => {
    const pwd = form.password;
    return (
        pwd.length >= 8 &&
        /[A-Z]/.test(pwd) &&
        /[0-9]/.test(pwd) &&
        /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pwd)
    );
});

const isFormValid = computed(() => {
    const passwordsMatch = form.password === form.password_confirmation && form.password !== '';
    return form.current_password !== '' && passwordRequirementsMet.value && passwordsMatch;
});

const updatePassword = () => {
    if (!isFormValid.value) {
        return;
    }

    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            if ($toastr) {
                $toastr.success('Votre mot de passe a été mis à jour avec succès.');
            }
        },
        onError: () => {
            if ($toastr && Object.keys(form.errors).length > 0) {
                const firstError = Object.values(form.errors)[0];
                if (firstError) {
                    $toastr.error(firstError);
                }
            }

            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <FormSection @submitted="updatePassword">
        <template #title>
            Mettre à jour le mot de passe
        </template>

        <template #description>
            Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="current_password" value="Mot de passe actuel" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password" value="Nouveau mot de passe" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <PasswordRequirements :password="form.password" />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <PrimaryButton :class="{ 'opacity-25': form.processing || !isFormValid }" :disabled="form.processing || !isFormValid">
                Enregistrer
            </PrimaryButton>
        </template>
    </FormSection>
</template>
