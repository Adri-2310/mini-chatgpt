<script setup>
import { Link } from '@inertiajs/vue3';
import { DropdownMenuItem } from '@/components/ui/ui/dropdown-menu';

const props = defineProps({
    href: String,
    as: String,
});

const emit = defineEmits(['action']);

const handleButtonClick = () => {
    emit('action');
};
</script>

<template>
    <DropdownMenuItem as-child>
        <button v-if="as == 'button'" type="button" class="w-full text-left px-2 py-1.5 text-sm cursor-pointer hover:bg-accent rounded-sm" @click="handleButtonClick">
            <slot />
        </button>

        <a v-else-if="as =='a'" :href="href" class="block w-full px-2 py-1.5 text-sm cursor-pointer hover:bg-accent rounded-sm">
            <slot />
        </a>

        <Link v-else :href="href" as="div" class="block w-full px-2 py-1.5 text-sm cursor-pointer hover:bg-accent rounded-sm">
            <slot />
        </Link>
    </DropdownMenuItem>
</template>
