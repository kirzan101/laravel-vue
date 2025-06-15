<template>
    <Head :title="title" />
    <main-layout>
        <UserGroupContent
            :permissions="permissions"
            :errors="errors"
            :flash="flash"
            :can="can"
        />
    </main-layout>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

import MainLayout from "../../Layouts/MainLayout.vue";
import UserGroupContent from "../../Components/Pages/System/UserGroup/UserGroupContent.vue";

defineProps({
    permissions: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});

const title = computed(() => {
    return `User Groups — ${appName.value}`;
});
</script>
