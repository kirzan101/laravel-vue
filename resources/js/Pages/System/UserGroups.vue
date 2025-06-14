<template>
    <main-layout> </main-layout>
</template>

<script setup>
import MainLayout from "../../Layouts/MainLayout.vue";

defineProps({
    userGroups: Array,
    per_page: Number,
    current_page: Number,
    total: Number,
    last_page: Number,
    search: String,
    sort_by: String,
    sort_direction: String,
    errors: Object,
    flash: Object,
    can: Array,
});
</script>
