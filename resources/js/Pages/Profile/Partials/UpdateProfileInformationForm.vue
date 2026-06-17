<script setup>
import { ref, watch, onMounted } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { inject } from 'vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const page = usePage();
const $toastr = inject('$toastr');

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const pendingEmailLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);
const emailJustVerified = ref(false);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => {
            clearPhotoFileInput();
            if ($toastr) {
                $toastr.success('Vos informations de profil ont été mises à jour avec succès.');
            }
        },
        onError: () => {
            if ($toastr && Object.keys(form.errors).length > 0) {
                const firstError = Object.values(form.errors)[0];
                if (firstError) {
                    $toastr.error(firstError);
                }
            }
        },
    });
};

const sendEmailVerification = () => {
    router.post(route('verification.send'), {}, {
        onSuccess: () => {
            verificationLinkSent.value = true;
        },
    });
};

const sendPendingEmailVerification = () => {
    router.post(route('verification.pending-email-send'), {}, {
        onSuccess: () => {
            pendingEmailLinkSent.value = true;
        },
    });
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};

// Afficher alerte si email vient d'être confirmé
onMounted(() => {
    if (page.props.flash?.status === 'email-change-verified') {
        emailJustVerified.value = true;
        setTimeout(() => {
            emailJustVerified.value = false;
        }, 8000);
    }
});
</script>

<template>
    <!-- Alerte email confirmé (AVANT FormSection) -->
    <div v-if="emailJustVerified" class="mb-4 p-4 bg-green-50 dark:bg-green-950 border-l-4 border-green-500 rounded-r-md">
        <p class="text-sm font-medium text-green-900 dark:text-green-100">
            ✅ <strong>Email confirmé avec succès !</strong>
        </p>
        <p class="text-sm text-green-800 dark:text-green-200 mt-1">
            Votre nouvelle adresse e-mail est maintenant : <strong>{{ user.email }}</strong>
        </p>
    </div>

    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Informations de Profil
        </template>

        <template #description>
            Mettez à jour vos informations de profil et votre adresse e-mail.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >

                <InputLabel for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div v-show="! photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full size-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'"
                    />
                </div>

                <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
                    Sélectionner une nouvelle photo
                </SecondaryButton>

                <SecondaryButton
                    v-if="user.profile_photo_path"
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                >
                    Supprimer la photo
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Nom" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" class="mt-2" />

                <!-- Vérification email principal -->
                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2">
                        Votre adresse e-mail n'est pas vérifiée.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-primary hover:text-primary/90 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                            @click.prevent="sendEmailVerification"
                        >
                            Cliquez ici pour renvoyer l'e-mail de vérification.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                        Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
                    </div>
                </div>

                <!-- Vérification nouvel email en attente -->
                <div v-if="user.pending_email" class="mt-4 p-4 bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-md">
                    <p class="text-sm text-blue-900 dark:text-blue-100">
                        ⏳ Vous avez demandé de changer votre adresse e-mail pour : <strong>{{ user.pending_email }}</strong>
                    </p>
                    <p class="text-sm text-blue-900 dark:text-blue-100 mt-2">
                        Un lien de confirmation a été envoyé. Vous avez 7 jours pour confirmer.

                        <Link
                            :href="route('verification.pending-email-send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-primary hover:text-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                            @click.prevent="sendPendingEmailVerification"
                        >
                            Renvoyer le lien
                        </Link>
                    </p>

                    <div v-show="pendingEmailLinkSent" class="mt-2 font-medium text-sm text-green-600">
                        Un nouveau lien de confirmation a été envoyé.
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Enregistrer
            </PrimaryButton>
        </template>
    </FormSection>
</template>
