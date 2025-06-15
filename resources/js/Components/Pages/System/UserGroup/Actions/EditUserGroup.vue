<template>
    <c-btn-edit v-if="showBtn" :label="item.name" @click="toggleDialog" />

    <c-dialog
        v-model="dialog"
        width="1000"
        title="Edit User Groups"
        prependIcon="mdi-pencil-circle"
        persistent
        @close="toggleDialog"
    >
        <c-container>
            <FormUserGroup
                :userGroup="item"
                :errors="errors"
                :flash="flash"
                :can="can"
            />
        </c-container>
    </c-dialog>
</template>

<script setup>
import { ref } from "vue";

import CBtnEdit from "@/Components/Customs/Buttons/CBtnEdit.vue";
import CDialog from "@/Components/Customs/Dialogs/CDialog.vue";
import CContainer from "@/Components/Customs/Containers/CContainer.vue";
import FormUserGroup from "./Forms/FormUserGroup.vue";

const dialog = ref(false);
const toggleDialog = () => {
    dialog.value = !dialog.value;
};

defineProps({
    item: {
        type: Object,
        required: true,
    },
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
