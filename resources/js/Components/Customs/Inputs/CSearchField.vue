<template>
    <c-text-field-solo
        placeholder="Search"
        prepend-inner-icon="mdi-magnify"
        v-bind="$attrs"
        :model-value="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
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
    maxCharacters: {
        type: Number,
        default: 50,
    },
});

const emit = defineEmits();
watch(
    () => props.modelValue,
    (newValue) => {
        // Check if the new value is a string and its length exceeds maxCharacters
        if (newValue && newValue.length > props.maxCharacters) {
            // If it exceeds, emit the trimmed value (slice it to maxCharacters)
            emit("update:modelValue", newValue.slice(0, props.maxCharacters));
        }
    }
);
</script>
