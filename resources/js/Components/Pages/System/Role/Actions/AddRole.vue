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
        title="Add Role"
        prependIcon="mdi-plus"
        persistent
        :btnDisabled="btnDisabled"
        @close="toggleDialog"
        @submit="handleSubmit"
    >
        <c-container>
            <FormRole
                :user_groups="user_groups"
                :errors="errors"
                :flash="flash"
                :can="can"
                @formValues="getFormValue"
                ref="formRoleRef"
            />

            <hr class="mt-4 mb-2 mx-5" />

            <FormTablePermissions
                :permissions="permissions"
                :modules="modules"
                :errors="errors"
                :flash="flash"
                :can="can"
                @selectedPermissions="getSelectedPermissions"
                ref="formTablePermissionsRef"
            />
        </c-container>
    </c-dialog>

    <SnackBar ref="snackBarRef" />
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

import SnackBar from "@/Components/Utilities/SnackBar.vue";
import FormRole from "./Forms/FormRole.vue";
import FormTablePermissions from "./Forms/FormTablePermissions.vue";

const dialog = ref(false);
const toggleDialog = () => {
    dialog.value = !dialog.value;
};

defineProps({
    showBtn: {
        type: Boolean,
        default: true,
    },
    permissions: Array,
    user_groups: Array,
    modules: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const form = ref({
    id: null,
    name: null,
    description: null,
    user_group_id: null,
    is_active: true,
    permissionIds: [],
});

// Get form values
const getFormValue = (value) => {
    Object.keys(form.value).forEach((key) => {
        if (Array.isArray(form.value[key])) {
            form.value[key] = value[key] ?? [];
        } else {
            form.value[key] = value[key] ?? null;
        }
    });
};

const formRef = ref(null);
const toggleFormRef = () => {
    if (formRef.value) {
        formRef.value.emitFormData();
    }
};

// permissions
const getSelectedPermissions = (selectedPermissions) => {
    form.value.permissionIds = [...selectedPermissions];
};

const formTablePermissionsRef = ref(null);
const toggleFormTablePermissionsRef = () => {
    if (formTablePermissionsRef.value) {
        formTablePermissionsRef.value.emitPermissionsData();
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
    toggleFormRef();
    toggleFormTablePermissionsRef();

    // submission here
    router.post("/roles", form.value, {
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
