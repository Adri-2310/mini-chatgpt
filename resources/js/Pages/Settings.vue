<script setup>
import { ref, inject } from 'vue';
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
    enabled: Boolean(props.customInstruction?.enabled ?? true),
});

const page = usePage();
const $toastr = inject('$toastr');

const submit = async () => {
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
            if ($toastr) {
                $toastr.success('Instructions personnalisées sauvegardées avec succès');
            }
        } else {
            const data = await response.json();
            const message = data.message || 'Une erreur est survenue lors de la sauvegarde';
            if ($toastr) {
                $toastr.error(message);
            }
        }
    } catch (err) {
        if ($toastr) {
            $toastr.error('Erreur réseau: impossible de se connecter au serveur');
        }
        console.error('Error:', err);
    }
};
</script>

<template>
    <Head title="Paramètres" />

    <div class="bg-background min-h-screen transition-colors">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <FormSection @submitted="submit">
                    <template #title>
                        Personnalisez SaveurIA
                    </template>

                    <template #description>
                        Définissez comment SaveurIA doit vous aider en cuisine. Spécifiez vos régimes, allergies, préférences culinaires et style d'apprentissage.
                    </template>

                    <template #form>
                        <div class="col-span-6">
                            <InputLabel for="instructions" value="Instructions (System Prompt)" />
                            <textarea
                                id="instructions"
                                v-model="form.instructions"
                                maxlength="2000"
                                rows="8"
                                placeholder="Ex: Je suis végétarien. J'aime les cuisines asiatiques et méditerranéennes. Donne-moi des recettes rapides (< 30 min), explique les techniques de base, et propose des substitutions d'ingrédients."
                                class="mt-1 block w-full px-4 py-2 border border-border bg-input text-foreground rounded-lg shadow-sm focus:ring-primary focus:border-primary"
                            ></textarea>
                            <p class="mt-2 text-sm text-muted-foreground">
                                {{ form.instructions.length }} / 2000 caractères
                            </p>
                        </div>

                        <div class="col-span-6">
                            <div class="flex items-center">
                                <Checkbox
                                    id="enabled"
                                    v-model:checked="form.enabled"
                                />
                                <label for="enabled" class="ml-2 block text-sm text-foreground">
                                    Appliquer mes préférences culinaires à toutes mes conversations
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
