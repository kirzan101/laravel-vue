<template>
    <nav-side-bar />

    <v-main>
        <slot />
    </v-main>

    <app-footer />
</template>

<script setup>
import NavSideBar from "./Navigation/NavSideBar.vue";
import AppFooter from "./Navigation/Components/AppFooter.vue";

import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const api_token = computed(() => page.props.token);

// Set token; added to run first in the browser
// This ensures that the token is available in localStorage for subsequent requests
if (typeof window !== "undefined") {
    const token = localStorage.getItem("token");
    if (!token && api_token.value) {
        localStorage.setItem("token", api_token.value);
    }
}
</script>
