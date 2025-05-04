<template>
    <v-btn
        class="ma-1"
        :icon="computedIcon"
        variant="text"
        density="compact"
        v-tooltip:bottom="computedTitle"
    ></v-btn>
</template>

<script setup>
import { computed } from "vue";
const props = defineProps({
    icon: String,
    title: String,
});

const computedIcon = computed(() => {
    return props.icon || "mdi-home";
});

const computedTitle = computed(() => {
    return props.title || "Home";
});
</script>
