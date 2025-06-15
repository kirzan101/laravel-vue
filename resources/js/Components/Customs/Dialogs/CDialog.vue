<template>
    <v-dialog v-bind="$attrs" :width="width">
        <v-card
            width="auto"
            max-width="1000"
            :prepend-icon="prependIcon"
            :title="title"
            :text="text"
        >
            <slot />
            <template v-slot:actions>
                <c-btn-text prepend-icon="mdi-close" @click="$emit('close')">
                    Close
                </c-btn-text>
                <c-btn-submit @click="$emit('submit')" />
            </template>
        </v-card>
    </v-dialog>
</template>

<script setup>
import CBtnText from "@/Components/Customs/Buttons/CBtnText.vue";
import CBtnSubmit from "@/Components/Customs/Buttons/CBtnSubmit.vue";

defineProps({
    width: {
        type: [String, Number],
        default: "auto",
    },
    title: {
        type: String,
        default: "Dialog",
    },
    text: String,
    prependIcon: {
        type: String,
        default: "mdi-update",
    },
});

defineEmits(["close", "submit"]);
</script>
