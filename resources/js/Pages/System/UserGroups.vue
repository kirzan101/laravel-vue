<template>
    <main-layout>
        <user-group-content :errors="errors" :flash="flash" :can="can" />
    </main-layout>
</template>

<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";
import UserGroupContent from "../../Components/Pages/System/UserGroup/UserGroupContent.vue";
import TableUserGroup from "../../Components/Pages/System/UserGroup/Tables/TableUserGroup.vue";

defineProps({
    errors: Object,
    flash: Object,
    can: Array,
});
</script>
