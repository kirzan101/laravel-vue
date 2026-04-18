<template>
    <c-btn-edit v-if="showBtn" :label="role.name" @click="toggleDialog" />

    <c-dialog
        v-model="dialog"
        width="1000"
        title="Edit Role"
        prependIcon="mdi-pencil-circle"
        persistent
        :btnDisabled="btnDisabled"
        @close="toggleDialog"
        @submit="handleSubmit"
    >
        <c-container>
            <FormRole
                :role="role"
                :user_groups="user_groups"
                :errors="errors"
                :flash="flash"
                :can="can"
                @formValues="getFormValue"
                ref="formRoleRef"
            />

            <hr class="mt-4 mb-2" />

            <FormTablePermissions
                :rolePermissions="rolePermissions"
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
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";

import FormRole from "./Forms/FormRole.vue";
import FormTablePermissions from "./Forms/FormTablePermissions.vue";
import SnackBar from "@/Components/Utilities/SnackBar.vue";

const dialog = ref(false);
const toggleDialog = () => {
    dialog.value = !dialog.value;
};

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
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

const rolePermissions = computed(() => {
    return props.role.rolePermissions || [];
});

const form = ref({
    id: null,
    name: null,
    code: null,
    description: null,
    permissionIds: [],
});

// Role form
const getFormValue = (value) => {
    Object.keys(form.value).forEach((key) => {
        if (Array.isArray(form.value[key])) {
            form.value[key] = value[key] ?? [];
        } else {
            form.value[key] = value[key] ?? null;
        }
    });
};

const formRoleRef = ref(null);
const toggleFormRoleRef = () => {
    if (formRoleRef.value) {
        formRoleRef.value.emitFormData();
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
    toggleFormRoleRef();
    toggleFormTablePermissionsRef();

    // submission here
    router.post(
        `/roles/${props.role.id}`,
        {
            _method: "PUT",
            forceFormData: true,
            ...form.value,
        },
        {
            onSuccess: ({ props }) => {
                dialog.value = false;

                toggleSnackBar(props.flash.success, "accent");
            },
            onError: () => {
                toggleSnackBar("Some fields has an error.", "error");
            },
            onBefore: () => {
                btnDisabled.value = true;
            },
            onFinish: () => {
                btnDisabled.value = false;
            },
        },
    );
};

defineExpose({
    toggleDialog,
});
</script>
