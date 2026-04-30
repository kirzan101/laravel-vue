<template>
    <c-chip :color="color" :icon="icon">
        {{ is_active ? "Active" : "Inactive" }}
    </c-chip>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    is_active: Boolean,
});

const icon = computed(() => {
    return props.is_active ? "mdi-check" : "mdi-close";
});

const color = computed(() => {
    return props.is_active ? "accent" : "red";
});
</script>
