<template>
    <v-snackbar v-model="snackBar" :color="props.color" :timeout="timeout">
        {{ props.message }}

        <template v-slot:actions="{ attrs }">
            <v-btn
                color="white"
                variant="text"
                v-bind="attrs"
                @click="toggleNotification(false)"
                icon="mdi-close-circle"
            >
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    message: {
        type: String,
        default: "Notification?",
    },
    timeout: {
        type: Number,
        default: 3000,
    },
    color: {
        type: String,
        default: "primary",
    },
});

const snackBar = ref(false);

const toggleNotification = () => {
    snackBar.value = true;
};

defineExpose({
    toggleNotification,
});
</script>
