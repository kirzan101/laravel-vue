<template>
    <Head :title="title" />
    <main-layout :errors="errors" :flash="flash" :can="can">
        <ProfileContent
            :user_groups="user_groups"
            :account_types="account_types"
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

import ProfileContent from "../../Components/Pages/System/Profile/ProfileContent.vue";

defineProps({
    user_groups: Array,
    account_types: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});

const title = computed(() => {
    return `Profiles — ${appName.value}`;
});
</script>
