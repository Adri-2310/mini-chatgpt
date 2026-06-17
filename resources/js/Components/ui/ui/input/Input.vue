<script setup>
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";

const props = defineProps({
  defaultValue: { type: [String, Number], required: false },
  modelValue: { type: [String, Number], required: false },
  class: {
    type: [Boolean, null, String, Object, Array],
    required: false,
    skipCheck: true,
  },
});

const emits = defineEmits(["update:modelValue"]);

const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
});
</script>

<template>
  <input
    v-model="modelValue"
    :class="
      cn(
        'appearance-none flex h-10 w-full rounded-md border border-transparent bg-input px-3 py-2 text-sm text-foreground file:bg-transparent file:text-foreground file:text-sm file:font-medium  focus-visible:outline-none    disabled:cursor-not-allowed disabled:opacity-50 autofill:bg-input autofill:text-foreground autofill:shadow-[inset_0_0_0px_1000px_var(--input)] autofill:shadow-none',
        props.class,
      )
    "
  />
</template>

