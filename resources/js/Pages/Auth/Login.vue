<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { inject } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const $toastr = inject('$toastr');

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onError: (errors) => {
            if ($toastr && Object.keys(errors).length > 0) {
                Object.values(errors).forEach(error => {
                    if (error) {
                        $toastr.error(error);
                    }
                });
            }
        },
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion" />

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
            <h1 class="text-2xl font-bold text-foreground mb-2">Connexion</h1>
            <p class="text-muted-foreground text-sm mb-6">Accédez à votre compte SaveurIA</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div v-if="status" class="rounded-lg bg-green-100 dark:bg-green-950 p-4 border border-green-200 dark:border-green-800">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ status }}</p>
                </div>

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
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-muted-foreground">Se souvenir de moi</span>
                </div>

                <PrimaryButton type="submit" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Connexion...' : 'Se connecter' }}
                </PrimaryButton>

                <Link v-if="canResetPassword" :href="route('password.request')" class="block text-sm text-center text-primary hover:underline">
                    Mot de passe oublié ?
                </Link>

                <div class="pt-4 border-t border-border text-center text-sm text-muted-foreground">
                    Pas encore de compte ?
                    <Link :href="route('register')" class="text-primary hover:underline font-semibold">
                        Créer un compte
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
