<template>
    <v-btn
        class="ma-1"
        :icon="computedIcon"
        variant="text"
        density="compact"
        v-tooltip:bottom="computedDescription"
        v-bind="$attrs"
    ></v-btn>
</template>

<script setup>
import { computed } from "vue";
const props = defineProps({
    icon: String,
    description: String,
});

const computedIcon = computed(() => {
    return props.icon || "mdi-home";
});

const computedDescription = computed(() => {
    return props.description || "Home";
});
</script>
