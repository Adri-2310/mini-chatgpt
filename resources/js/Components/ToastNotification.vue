<script setup>
import { usePage } from '@inertiajs/vue3';
import { watch, onMounted, ref } from 'vue';

const page = usePage();
const props = defineProps({
    status: String,
});

const message = ref('');
const messageType = ref('success');
const showNotification = ref(false);

const displayToast = (msg, type = 'success') => {
    if (msg) {
        message.value = msg;
        messageType.value = type;
        showNotification.value = true;
        setTimeout(() => {
            showNotification.value = false;
        }, 5000);
    }
};

onMounted(() => {
    if (props.status) {
        displayToast(props.status, 'success');
    }
    if (page.props.flash?.status) {
        displayToast(page.props.flash.status, 'success');
    }
    if (page.props.flash?.error) {
        displayToast(page.props.flash.error, 'error');
    }
});

watch(
    () => page.props.flash?.status,
    (newStatus) => {
        if (newStatus) {
            displayToast(newStatus, 'success');
        }
    }
);

watch(
    () => page.props.flash?.error,
    (newError) => {
        if (newError) {
            displayToast(newError, 'error');
        }
    }
);
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
    >
        <div v-if="showNotification" class="fixed top-4 right-4 z-50">
            <div
                class="px-6 py-4 rounded-lg shadow-lg text-white font-medium"
                :class="messageType === 'success' ? 'bg-green-500' : 'bg-red-500'"
            >
                {{ message }}
            </div>
        </div>
    </Transition>
</template>
