<template>
    {{ moduleName }}
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
});

const moduleName = computed(() => {
    if (props.name === null || props.name === "") {
        return "Permission Default";
    }

    return props.name
        .split("_") // Split the string by underscores
        .map((word) => {
            // Capitalize the first letter of each word and lowercase the rest
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        })
        .join(" "); // Join the words with a space
});
</script>
