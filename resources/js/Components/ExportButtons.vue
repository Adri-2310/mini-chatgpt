<script setup>
import { inject } from 'vue';

const props = defineProps({
    conversationId: Number,
    conversationTitle: String,
});

const $toastr = inject('$toastr');

const exportConversation = async (format) => {
    if (!props.conversationId) return;

    try {
        const url = `/conversations/${props.conversationId}/export?format=${format}`;
        const response = await fetch(url);

        if (response.ok) {
            const blob = await response.blob();
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = `${props.conversationTitle || 'conversation'}.${format === 'markdown' ? 'md' : 'json'}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(downloadUrl);

            if ($toastr) {
                $toastr.success(`Conversation exportée en ${format.toUpperCase()}`);
            }
        } else {
            if ($toastr) {
                $toastr.error('Erreur lors de l\'export');
            }
        }
    } catch (err) {
        console.error('Erreur export:', err);
        if ($toastr) {
            $toastr.error('Erreur lors de l\'export');
        }
    }
};
</script>

<template>
    <div v-if="conversationId" class="flex gap-2">
        <button
            @click="exportConversation('markdown')"
            title="Exporter en Markdown"
            class="px-3 py-2 text-sm bg-secondary text-secondary-foreground hover:bg-secondary/80 rounded-lg transition"
        >
            📄 MD
        </button>
        <button
            @click="exportConversation('json')"
            title="Exporter en JSON"
            class="px-3 py-2 text-sm bg-secondary text-secondary-foreground hover:bg-secondary/80 rounded-lg transition"
        >
            { } JSON
        </button>
    </div>
</template>
