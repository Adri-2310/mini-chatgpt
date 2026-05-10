<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { watch, onMounted, inject } from 'vue';

const page = usePage();
const $toastr = inject('$toastr');

const props = defineProps({
    status: String,
});

const showToast = (message, type = 'success') => {
    if (message && $toastr) {
        $toastr[type](message);
    }
};

onMounted(() => {
    if (props.status) {
        showToast(props.status, 'success');
    }
    if (page.props.flash?.status) {
        showToast(page.props.flash.status, 'success');
    }
    if (page.props.flash?.error) {
        showToast(page.props.flash.error, 'error');
    }
});

watch(
    () => page.props.flash?.status,
    (newStatus) => {
        if (newStatus) {
            showToast(newStatus, 'success');
        }
    }
);

watch(
    () => page.props.flash?.error,
    (newError) => {
        if (newError) {
            showToast(newError, 'error');
        }
    }
);
</script>

<template>
    <!-- Toastr notifications handled globally -->
</template>
