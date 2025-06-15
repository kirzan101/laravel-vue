<template>
    <c-btn-edit v-if="showBtn" :label="userGroup.name" @click="toggleDialog" />

    <c-dialog
        v-model="dialog"
        width="1000"
        title="Edit User Groups"
        prependIcon="mdi-pencil-circle"
        persistent
        :btnDisabled="btnDisabled"
        @close="toggleDialog"
        @submit="handleSubmit"
    >
        <c-container>
            <FormUserGroup
                :userGroup="userGroup"
                :errors="errors"
                :flash="flash"
                :can="can"
                @formValues="getFormUserGroupValue"
                ref="formUserGroupRef"
            />

            <hr class="mt-4 mb-2" />

            <FormTablePermissions
                :userGroupPermissions="userGroupPermissions"
                :permissions="permissions"
                :errors="errors"
                :flash="flash"
                :can="can"
                @selectedPermissions="getSelectedPermissions"
                ref="formTablePermissionsRef"
            />
        </c-container>
    </c-dialog>
</template>

<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";

// custom components
import CBtnEdit from "@/Components/Customs/Buttons/CBtnEdit.vue";
import CDialog from "@/Components/Customs/Dialogs/CDialog.vue";
import CContainer from "@/Components/Customs/Containers/CContainer.vue";

import FormUserGroup from "./Forms/FormUserGroup.vue";
import FormTablePermissions from "./Forms/FormTablePermissions.vue";

const dialog = ref(false);
const toggleDialog = () => {
    dialog.value = !dialog.value;
};

const props = defineProps({
    userGroup: {
        type: Object,
        required: true,
    },
    showBtn: {
        type: Boolean,
        default: true,
    },
    permissions: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const userGroupPermissions = computed(() => {
    return props.userGroup.userGroupPermissions || [];
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

// handle submission
const btnDisabled = ref(false);
const handleSubmit = () => {
    toggleFormUserGroupRef();
    toggleFormTablePermissionsRef();

    // submission here
    router.post(
        `/user-groups/${props.userGroup.id}`,
        {
            _method: "PUT",
            forceFormData: true,
            ...form.value,
        },
        {
            onSuccess: ({ props }) => {
                dialog.value = false;
            },
            onError: () => {
                emits("notification", "Some fields has an error.", "error");
            },
            onBefore: () => {
                btnDisabled.value = true;
            },
            onFinish: () => {
                btnDisabled.value = false;
            },
        }
    );
};

defineExpose({
    toggleDialog,
});
</script>
