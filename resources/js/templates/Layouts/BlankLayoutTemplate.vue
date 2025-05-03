<template>
    <nav-bar />

    <v-main>
        <slot />
    </v-main>
</template>

<script setup>
import { ref } from "vue";
import NavBar from "./Navigation/NavBarTemplate.vue";
</script>
