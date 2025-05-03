<template>
    <nav-bar />

    <v-main>
        <slot />
    </v-main>
</template>

<script setup>
import { ref } from "vue";
import NavBar from "./Navigation/NavBar.vue";
</script>
