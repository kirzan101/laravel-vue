<template>
    <v-empty-state
        headline="Whoops, 401"
        :title="title"
        :text="text"
        image="/images/errors/error_401.svg"
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
