<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { inject, computed } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import PasswordRequirements from '@/Components/PasswordRequirements.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const $toastr = inject('$toastr');

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
    const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email);
    const passwordsMatch = form.password === form.password_confirmation && form.password !== '';

    return (
        form.name.trim() !== '' &&
        isEmailValid &&
        passwordRequirementsMet.value &&
        passwordsMatch &&
        (!form.terms || form.terms === true)
    );
});

const submit = () => {
    if (!isFormValid.value) {
        return;
    }

    form.post(route('register'), {
        onError: (errors) => {
            if ($toastr && Object.keys(errors).length > 0) {
                const firstError = Object.values(errors)[0];
                if (firstError) {
                    $toastr.error(firstError);
                }
            }
        },
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Inscription" />

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
            <h1 class="text-2xl font-bold text-foreground mb-2">Créer un compte</h1>
            <p class="text-muted-foreground text-sm mb-6">Rejoignez SaveurIA et commencez à cuisiner</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel for="name" value="Nom complet" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Jean Dupont"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="votre@email.com"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Mot de passe" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    />
                    <PasswordRequirements :password="form.password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                    <div class="flex items-start gap-2">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />
                        <div class="text-sm text-muted-foreground">
                            J'accepte les
                            <a target="_blank" :href="route('terms.show')" class="text-primary hover:underline">
                                Conditions de Service
                            </a>
                            et la
                            <a target="_blank" :href="route('policy.show')" class="text-primary hover:underline">
                                Politique de Confidentialité
                            </a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </div>

                <PrimaryButton type="submit" class="w-full" :disabled="form.processing || !isFormValid">
                    {{ form.processing ? 'Inscription...' : 'S\'inscrire' }}
                </PrimaryButton>

                <div class="text-center text-sm text-muted-foreground">
                    Déjà inscrit ?
                    <Link :href="route('login')" class="text-primary hover:underline font-semibold">
                        Se connecter
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
