<template>
    <v-app-bar color="primary">
        <v-app-bar-nav-icon v-if="hasDrawer" @click="toggleDrawer" />

        <v-app-bar-title>{{ appName }}</v-app-bar-title>

        <template v-slot:append>
            <switch-theme />
            <menu-settings />
        </template>
    </v-app-bar>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

import SwitchTheme from "./Components/SwitchThemeTemplate.vue";
import MenuSettings from "./Components/MenuSettingsTemplate.vue";

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});

const props = defineProps({
    hasDrawer: {
        type: Boolean,
        default: false,
    },
});

const emits = defineEmits(["toggleDrawer"]);
const toggleDrawer = () => {
    emits("toggleDrawer");
};
</script>
