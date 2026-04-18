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
        title="Add User Group"
        prependIcon="mdi-plus"
        persistent
        :btnDisabled="btnDisabled"
        @close="toggleDialog"
        @submit="handleSubmit"
    >
        <c-container>
            <FormUserGroup
                :user_group_types="user_group_types"
                :errors="errors"
                :flash="flash"
                :can="can"
                @formValues="getFormUserGroupValue"
                ref="formUserGroupRef"
            />
        </c-container>
    </c-dialog>

    <SnackBar ref="snackBarRef" />
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

// Importing custom components
import FormUserGroup from "./Forms/FormUserGroup.vue";
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
    user_group_types: Array,
    modules: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const form = ref({
    id: null,
    name: null,
    code: null,
    description: null,
});

// User group form
const getFormUserGroupValue = (value) => {
    Object.keys(form.value).forEach((key) => {
        if (Array.isArray(form.value[key])) {
            form.value[key] = value[key] ?? [];
        } else {
            form.value[key] = value[key] ?? null;
        }
    });
};

const formUserGroupRef = ref(null);
const toggleFormUserGroupRef = () => {
    if (formUserGroupRef.value) {
        formUserGroupRef.value.emitFormData();
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
    toggleFormUserGroupRef();

    // submission here
    router.post("/user-groups", form.value, {
        forceFormData: true,
        onSuccess: ({ props }) => {
            toggleDialog();

            Object.keys(form.value).forEach((key) => {
                if (Array.isArray(form.value[key])) {
                    form.value[key] = [];
                } else {
                    form.value[key] = null;
                }
            });

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
