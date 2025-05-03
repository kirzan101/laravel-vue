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
</script>
