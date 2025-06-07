<template>
    <v-empty-state
        headline="Whoops, 404"
        :title="title"
        :text="text"
        image="/images/errors/error_404.svg"
    >
        <Link href="/">
            <c-btn prepend-icon="mdi-home">Home</c-btn>
        </Link>
    </v-empty-state>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import CBtn from "../Customs/Buttons/CBtn.vue";

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
