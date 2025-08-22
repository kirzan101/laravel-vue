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
        title="Add Profile"
        prependIcon="mdi-plus"
        persistent
        :btnDisabled="btnDisabled"
        @close="toggleDialog"
        @submit="handleSubmit"
    >
        <c-container>
            <FormProfile
                :user_groups="user_groups"
                :account_types="account_types"
                :errors="errors"
                :flash="flash"
                :can="can"
                @formValues="getFormProfileValue"
                ref="formProfileRef"
            />
        </c-container>
    </c-dialog>

    <SnackBar ref="snackBarRef" />
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

// Importing custom components
import FormProfile from "./Forms/FormProfile.vue";
import SnackBar from "@/Components/Utilities/SnackBar.vue";

const dialog = ref(false);
const toggleDialog = () => {
    dialog.value = !dialog.value;
};

defineProps({
    showBtn: {
        type: Boolean,
        default: true,
    },
    user_groups: Array,
    account_types: Array,
    errors: Object,
    flash: Object,
    can: Array,
});
const form = ref({});

// User group form
const getFormProfileValue = (value) => {
    Object.assign(form.value, value);
};

const formProfileRef = ref(null);
const toggleFormProfileRef = () => {
    if (formProfileRef.value) {
        formProfileRef.value.emitFormData();
    }
};

// notification
const snackBarRef = ref(null);
const toggleSnackBar = (message, color) => {
    if (!snackBarRef.value) {
        return;
    }

    snackBarRef.value.showNotification(message, color);
};

// handle submission
const btnDisabled = ref(false);
const handleSubmit = () => {
    toggleFormProfileRef();
    
    // submission here
    router.post("/profiles", form.value, {
        forceFormData: true,
        onSuccess: ({ props }) => {
            toggleDialog();

            // reset form value
            form.value = {};

            toggleSnackBar(props.flash.success, "accent");
        },
        onError: () => {
            // emits("notification", "Some fields has an error.", "error");
            toggleSnackBar("Some fields has an error.", "error");
        },
        onBefore: () => {
            btnDisabled.value = true;
        },
        onFinish: () => {
            btnDisabled.value = false;
        },
    });
};

defineExpose({
    toggleDialog,
});
</script>
