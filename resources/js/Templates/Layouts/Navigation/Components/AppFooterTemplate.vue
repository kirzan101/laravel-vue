<template>
    <c-footer app color="transparent" class="d-flex justify-center" height="54">
        <span class="white--text"
            >&copy; {{ `${new Date().getFullYear()} ${appName}` }}</span
        >
    </c-footer>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});
</script>
