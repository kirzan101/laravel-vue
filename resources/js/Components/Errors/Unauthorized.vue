<template>
    <v-empty-state
        headline="Whoops, 401"
        :title="title"
        :text="text"
        image="/images/errors/error_401.svg"
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
        default: "Server Error",
    },
    text: {
        type: String,
        default: "Contact the administrator or try again later.",
    },
});
</script>
