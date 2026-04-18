<template>
    <Head :title="title" />
    <main-layout :errors="errors" :flash="flash" :can="can">
        <RoleContent
            :permissions="permissions"
            :user_groups="user_groups"
            :moduleLists="moduleLists"
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
import RoleContent from "../../Components/Pages/System/Role/RoleContent.vue";

defineProps({
    permissions: Array,
    user_groups: Array,
    moduleLists: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});

const title = computed(() => {
    return `Roles — ${appName.value}`;
});
</script>
