<template>
    <Head :title="title" />
    <main-layout :errors="errors" :flash="flash" :can="can">
        {{ categories }}
    </main-layout>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

import MainLayout from "../../Layouts/MainLayout.vue";

defineProps({
    categories: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});

const title = computed(() => {
    return `Modules — ${appName.value}`;
});
</script>
