<template>
    <c-btn-edit
        v-if="showBtn && hasAccess"
        :label="profile.name"
        @click="toggleDialog"
    />
    <p v-else class="text-wrap">{{ profile.name }}</p>

    <c-dialog
        v-model="dialog"
        width="1000"
        title="Edit Profile"
        prependIcon="mdi-pencil-circle"
        persistent
        :btnDisabled="btnDisabled"
        :submittable="hasAccess"
        @close="toggleDialog"
        @submit="handleSubmit"
    >
        <c-container>
            <FormProfile
                :profile="profile"
                :user_groups="user_groups"
                :roles="roles"
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
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";

import FormProfile from "./Forms/FormProfile.vue";
import SnackBar from "@/Components/Utilities/SnackBar.vue";

const dialog = ref(false);
const toggleDialog = () => {
    dialog.value = !dialog.value;
};

const props = defineProps({
    profile: {
        type: Object,
        required: true,
    },
    showBtn: {
        type: Boolean,
        default: true,
    },
    user_groups: Array,
    roles: Array,
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

const hasAccess = computed(() => {
    return props.can.includes("update-profiles");
});

// handle submission
const btnDisabled = ref(false);
const handleSubmit = () => {
    toggleFormProfileRef();

    // submission here
    router.post(
        `/profiles/${props.profile.id}`,
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
