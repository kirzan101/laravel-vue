<template>
    <c-dialog
        v-model="dialog"
        :width="width"
        :title="title"
        :text="text"
        prependIcon="mdi-alert-circle"
        closable
        @close="toggleDialogVisibilty(false)"
        @submit="handleConfirm"
    >
    </c-dialog>
</template>

<script setup>
import { ref } from "vue";
import CDialog from "../Customs/Dialogs/CDialog.vue";

const props = defineProps({
    title: {
        default: "Notice",
    },
    text: {
        default: "Confirm action?",
        type: String,
    },
    width: {
        type: [String, Number],
        default: 400,
    },
});

const dialog = ref(false);

const emits = defineEmits(["close", "submit"]);
const toggleDialogVisibilty = (value = false) => {
    dialog.value = value;
};

const handleConfirm = () => {
    toggleDialogVisibilty();
    emits("submit");
};

defineExpose({
    toggleDialogVisibilty,
});
</script>
