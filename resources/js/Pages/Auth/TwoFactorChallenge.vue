<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Head title="Confirmation à Deux Facteurs" />

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
            <h1 class="text-2xl font-bold text-foreground mb-2">Authentification à Deux Facteurs</h1>
            <p class="text-muted-foreground text-sm mb-6">
                <template v-if="! recovery">
                    Entrez le code d'authentification fourni par votre application authenticateur.
                </template>

                <template v-else>
                    Entrez l'un de vos codes de récupération d'urgence.
                </template>
            </p>

            <form @submit.prevent="submit" class="space-y-5">
                <div v-if="! recovery">
                    <InputLabel for="code" value="Code d'authentification" />
                    <TextInput
                        id="code"
                        ref="codeInput"
                        v-model="form.code"
                        type="text"
                        inputmode="numeric"
                        placeholder="000000"
                        autofocus
                        autocomplete="one-time-code"
                    />
                    <InputError class="mt-2" :message="form.errors.code" />
                </div>

                <div v-else>
                    <InputLabel for="recovery_code" value="Code de Récupération" />
                    <TextInput
                        id="recovery_code"
                        ref="recoveryCodeInput"
                        v-model="form.recovery_code"
                        type="text"
                        placeholder="xxxxxxxx-xxxx-xxxx"
                        autocomplete="one-time-code"
                    />
                    <InputError class="mt-2" :message="form.errors.recovery_code" />
                </div>

                <PrimaryButton type="submit" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Vérification...' : 'Se connecter' }}
                </PrimaryButton>

                <button type="button" class="w-full text-sm text-primary hover:underline text-center transition" @click.prevent="toggleRecovery">
                    <template v-if="! recovery">
                        Utiliser un code de récupération
                    </template>

                    <template v-else>
                        Utiliser un code d'authentification
                    </template>
                </button>
            </form>
        </div>
    </div>
</template>
