<template>
    <v-icon @click.stop="toggleConfirm(true)">mdi-close-circle</v-icon>

    <confirm-dialog
        v-model="dialog"
        ref="confirmDialogRef"
        title="Warning"
        :text="`Remove ${selectedImage.file_name}?`"
        @submit="handleDeleteAttachment(selectedImage)"
        @close="toggleDialogVisibilty"
    ></confirm-dialog>

    <snack-bar ref="notificationRef" />
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import ConfirmDialog from "../../ConfirmDialog.vue";
import SnackBar from "../../SnackBar.vue";

const dialog = ref(false);

const props = defineProps({
    selectedImage: {
        type: Object,
        required: true,
    },
    attachmentLink: {
        type: String,
        required: true,
    },
});

const toggleDialogVisibilty = (value = false) => {
    dialog.value = value;
};

const notificationRef = ref(null);
const toggleNotification = (message = "message", color = "primary") => {
    if (notificationRef.value) {
        notificationRef.value.showNotification(message, color);
    }
};

const handleDeleteAttachment = (attachment) => {
    const deleteLink = `/${props.attachmentLink}/${attachment.id}`;

    router.delete(deleteLink, {
        onSuccess: () => {
            toggleNotification("Attachment deleted successfully.", "success");
            toggleDialogVisibilty(); // Close the dialog after successful deletion
        },
        onError: () => {
            toggleNotification("Some fields has an error.", "error");
        },
    });
};

defineExpose({
    toggleDialogVisibilty,
});
</script>
