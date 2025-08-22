<template>
    <c-combobox
        label="Contact Numbers"
        v-model="contactNumberInputs"
        multiple
        chips
        closable-chips
        v-bind="$attrs"
        :hint="`Enter up to ${maxInput} valid contact numbers`"
    />
</template>

<script setup>
import CCombobox from "@/Components/Customs/Comboboxes/CCombobox.vue";
import { ref, watch } from "vue";

const props = defineProps({
    contactNumbers: {
        type: Array,
        default: () => [],
    },
    maxInput: {
        type: [Number, String],
        default: 3,
    },
});

const emit = defineEmits(["filteredContactNo"]);

// 🟡 Separate raw input from validated output
const contactNumberInputs = ref(null);

// Sync prop changes from parent → internal input
watch(
    () => props.contactNumbers,
    (newVal) => {
        if (
            JSON.stringify(contactNumberInputs.value) !== JSON.stringify(newVal)
        ) {
            contactNumberInputs.value = [...newVal];
        }
    },
    { immediate: true }
);

//
watch(
    contactNumberInputs,
    (newVal) => {
        const validated = (Array.isArray(newVal) ? newVal : [])
            .filter(
                (item) =>
                    typeof item === "string" &&
                    item.length >= 11 &&
                    item.length <= 13
            )
            .slice(0, Number(props.maxInput));

        emit("filteredContactNo", [...validated]);
    },
    { deep: true }
);
</script>
