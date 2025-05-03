<template>
    <nav-side-bar />

    <v-main>
        <slot />
    </v-main>

    <app-footer />
</template>

<script setup>
import NavSideBar from "./Navigation/NavSideBarTemplate.vue";
import AppFooter from "./Navigation/Components/AppFooterTemplate.vue";
</script>
