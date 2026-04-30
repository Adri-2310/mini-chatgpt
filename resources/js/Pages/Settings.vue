<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineOptions({
  layout: AppLayout,
  layoutProps: {
    title: 'Paramètres'
  }
});

const props = defineProps({
    customInstruction: Object,
});

const form = ref({
    instructions: props.customInstruction?.instructions || '',
    enabled: props.customInstruction?.enabled ?? true,
});

const page = usePage();
const submitted = ref(false);
const error = ref('');

const submit = async () => {
    error.value = '';
    submitted.value = false;

    try {
        const response = await fetch('/settings/instructions', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token,
            },
            body: JSON.stringify(form.value),
        });

        if (response.ok) {
            submitted.value = true;
            setTimeout(() => {
                submitted.value = false;
            }, 3000);
        } else {
            const data = await response.json();
            error.value = data.message || 'Une erreur est survenue lors de la sauvegarde';
        }
    } catch (err) {
        error.value = 'Erreur réseau: impossible de se connecter au serveur';
        console.error('Error:', err);
    }
};
</script>

<template>
    <Head title="Paramètres" />

    <div class="bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 min-h-screen">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Message d'erreur -->
                <div
                    v-if="error"
                    class="mb-4 px-4 py-3 bg-red-900/30 border border-red-500/50 text-red-300 rounded-lg"
                >
                    ✗ {{ error }}
                </div>

                <!-- Message de succès -->
                <div
                    v-if="submitted"
                    class="mb-4 px-4 py-3 bg-green-900/30 border border-green-500/50 text-green-300 rounded-lg"
                >
                    ✓ Instructions personnalisées sauvegardées avec succès
                </div>

                <FormSection @submitted="submit">
                    <template #title>
                        Instructions Personnalisées
                    </template>

                    <template #description>
                        Configurez vos instructions personnalisées qui seront automatiquement appliquées à toutes vos conversations.
                    </template>

                    <template #form>
                        <div class="col-span-6">
                            <InputLabel for="instructions" value="Instructions (System Prompt)" />
                            <textarea
                                id="instructions"
                                v-model="form.instructions"
                                maxlength="2000"
                                rows="8"
                                placeholder="Ex: Tu es un expert en développement web. Réponds toujours en français et fournis des exemples de code quand approprié."
                                class="mt-1 block w-full px-4 py-2 border border-slate-600 bg-slate-700 text-white rounded-lg shadow-sm focus:ring-blue-400 focus:border-blue-400"
                            ></textarea>
                            <p class="mt-2 text-sm text-slate-400">
                                {{ form.instructions.length }} / 2000 caractères
                            </p>
                        </div>

                        <div class="col-span-6">
                            <div class="flex items-center">
                                <Checkbox
                                    id="enabled"
                                    v-model:checked="form.enabled"
                                />
                                <label for="enabled" class="ml-2 block text-sm text-slate-300">
                                    Activer les instructions personnalisées
                                </label>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <PrimaryButton @click="submit">
                            Sauvegarder
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </div>
</template>
