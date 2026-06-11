<script setup>
defineProps({
    models: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: String,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label for="model" class="block text-sm font-medium text-muted-foreground mb-2">
            Sélectionner un modèle
        </label>
        <select
            id="model"
            :value="modelValue"
            :disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'w-full px-4 py-2 bg-input border border-border text-foreground rounded-lg focus:border-primary focus:ring-2 focus:ring-primary focus:ring-offset-0 transition',
                disabled ? 'opacity-50 cursor-not-allowed' : ''
            ]"
        >
            <option value="">-- Choisir un modèle --</option>
            <option v-for="model in models" :key="model.id" :value="model.model_id">
                {{ model.name }} ({{ model.provider }})
            </option>
        </select>
    </div>
</template>
