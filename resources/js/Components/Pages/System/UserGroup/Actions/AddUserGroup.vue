<template>
    <c-btn-add
        v-if="showBtn"
        class="mx-2"
        size="default"
        @click="toggleDialog"
    />

    <c-dialog
        v-model="dialog"
        width="1000"
        title="Add User Groups"
        prependIcon="mdi-plus"
        persistent
        @close="toggleDialog"
    >
        <c-container>
            <FormUserGroup :errors="errors" :flash="flash" :can="can" />
        </c-container>
    </c-dialog>
</template>

<script setup>
import { ref } from "vue";

import CBtnAdd from "@/Components/Customs/Buttons/CBtnAdd.vue";
import CDialog from "@/Components/Customs/Dialogs/CDialog.vue";
import CContainer from "@/Components/Customs/Containers/CContainer.vue";
import FormUserGroup from "./Forms/FormUserGroup.vue";

const dialog = ref(false);
const toggleDialog = () => {
    dialog.value = !dialog.value;
};

defineProps({
    showBtn: {
        type: Boolean,
        default: true,
    },
    errors: Object,
    flash: Object,
    can: Array,
});

defineExpose({
    toggleDialog,
});
</script>
