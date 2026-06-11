<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Réinitialiser le mot de passe" />

    <ToastNotification :status="$page.props.flash?.status" />

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
            <h1 class="text-2xl font-bold text-foreground mb-2">Réinitialiser le mot de passe</h1>
            <p class="text-muted-foreground text-sm mb-6">Créez un nouveau mot de passe pour votre compte.</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="votre@email.com"
                        required
                        autofocus
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

                <PrimaryButton type="submit" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Réinitialisation...' : 'Réinitialiser le mot de passe' }}
                </PrimaryButton>
            </form>
        </div>
    </div>
</template>
