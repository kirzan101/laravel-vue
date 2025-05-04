<template>
    <c-btn-icon
        icon="mdi-clipboard-multiple"
        color="primary"
        size="x-small"
        @click="copyText"
        description="copy"
    >
    </c-btn-icon>
    <snack-bar
        :message="notificationMessage"
        :color="notificationColor"
        ref="snackBarRef"
    />
</template>
<script setup>
import { ref } from "vue";
import CBtnIcon from "../Customs/Buttons/CBtnIcon.vue";
import SnackBar from "./SnackBar.vue";

const props = defineProps({
    textValue: String,
});

const notificationMessage = ref(null);
const notificationColor = ref(null);
const snackBarRef = ref(null);
const toggleNotification = () => {
    if (!snackBarRef.value) {
        return;
    }

    snackBarRef.value.toggleNotification();
};

const copyText = async () => {
    try {
        await navigator.clipboard.writeText(props.textValue);

        notificationColor.value = "accent";
        notificationMessage.value = "Successfully copied!";
    } catch (err) {
        console.warn("Failed to copy text: ", err);

        notificationColor.value = "error";
        notificationMessage.value = "Error while copying!";
    } finally {
        toggleNotification();
    }
};
</script>
