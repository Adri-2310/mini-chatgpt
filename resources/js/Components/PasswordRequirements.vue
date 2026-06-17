<script setup>
import { computed } from 'vue';

const props = defineProps({
    password: {
        type: String,
        default: '',
    },
});

const requirements = computed(() => [
    {
        label: 'Minimum 8 caractères',
        met: props.password.length >= 8,
    },
    {
        label: 'Au moins une lettre majuscule (A-Z)',
        met: /[A-Z]/.test(props.password),
    },
    {
        label: 'Au moins un chiffre (0-9)',
        met: /[0-9]/.test(props.password),
    },
    {
        label: 'Au moins un caractère spécial (!@#$%^&*)',
        met: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(props.password),
    },
]);

const allRequirementsMet = computed(() => requirements.value.every(req => req.met));
</script>

<template>
    <div class="mt-3 p-3 rounded-md bg-secondary/30 border border-border/50">
        <p class="text-xs font-medium text-foreground mb-2">Critères du mot de passe :</p>
        <div class="space-y-1.5">
            <div v-for="(req, index) in requirements" :key="index" class="flex items-center gap-2 text-xs">
                <div :class="['w-4 h-4 rounded-full flex items-center justify-center', req.met ? 'bg-green-500' : 'bg-muted']">
                    <span v-if="req.met" class="text-white text-[10px]">✓</span>
                </div>
                <span :class="req.met ? 'text-foreground' : 'text-muted-foreground'">
                    {{ req.label }}
                </span>
            </div>
        </div>
    </div>
</template>
