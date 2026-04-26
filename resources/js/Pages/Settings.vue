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

    <div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Message d'erreur -->
                <div
                    v-if="error"
                    class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg"
                >
                    ✗ {{ error }}
                </div>

                <!-- Message de succès -->
                <div
                    v-if="submitted"
                    class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg"
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
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ form.instructions.length }} / 2000 caractères
                            </p>
                        </div>

                        <div class="col-span-6">
                            <div class="flex items-center">
                                <Checkbox
                                    id="enabled"
                                    v-model:checked="form.enabled"
                                />
                                <label for="enabled" class="ml-2 block text-sm text-gray-700">
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
