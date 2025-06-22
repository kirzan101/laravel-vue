<template>
    <Head :title="title" />
    <main-layout>
        <UserGroupContent
            :permissions="permissions"
            :user_group_types="user_group_types"
            :modules="modules"
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
    user_group_types: Array,
    modules: Array,
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
