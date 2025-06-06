<template>
    <v-empty-state
        headline="Whoops, 404"
        :title="title"
        :text="text"
        image="/images/errors/error_404.svg"
    >
    </v-empty-state>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: "Not Found",
    },
    text: {
        type: String,
        default: "Attachment not found",
    },
});
</script>
