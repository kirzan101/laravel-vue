<template>
    <c-text-field-solo
        placeholder="Search"
        prepend-inner-icon="mdi-magnify"
        v-bind="$attrs"
        :model-value="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
        rounded="pill"
        bg-color="search-bg"
    >
        <slot />
    </c-text-field-solo>
</template>

<script setup>
import { watch, computed } from "vue";
import CTextFieldSolo from "./CTextFieldSolo.vue";

// Define props
const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null,
    },
});

const emit = defineEmits();
</script>
