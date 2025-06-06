<template>
    <v-empty-state
        headline="Whoops, 500"
        :title="title"
        :text="text"
        image="/images/errors/error_500.svg"
    >
    </v-empty-state>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: "Server Error",
    },
    text: {
        type: String,
        default: "Contact the administrator or try again later.",
    },
});
</script>
