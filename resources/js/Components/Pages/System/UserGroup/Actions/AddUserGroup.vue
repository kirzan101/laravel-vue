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
                :errors="errors"
                :flash="flash"
                :can="can"
                @formValues="getFormUserGroupValue"
                ref="formUserGroupRef"
            />

            <hr class="mt-4 mb-2 mx-5" />

            <FormTablePermissions
                :permissions="permissions"
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

// Importing custom components
import CBtnAdd from "@/Components/Customs/Buttons/CBtnAdd.vue";
import CDialog from "@/Components/Customs/Dialogs/CDialog.vue";
import CContainer from "@/Components/Customs/Containers/CContainer.vue";
import FormUserGroup from "./Forms/FormUserGroup.vue";
import FormTablePermissions from "./Forms/FormTablePermissions.vue";
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
    permissions: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const form = ref({
    id: null,
    name: null,
    code: null,
    description: null,
    permissions: [],
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

// permissions
const getSelectedPermissions = (selectedPermissions) => {
    form.value.permissions = [...selectedPermissions];
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
    toggleFormUserGroupRef();
    toggleFormTablePermissionsRef();

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

            toggleSnackBar(props.flash.success, "success");
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
