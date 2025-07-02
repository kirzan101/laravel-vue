<template>
    <c-dialog
        persistent
        v-model="dialog"
        width="400"
        title="Change password"
        prepend-icon="mdi-lock-reset"
        :btnDisabled="loading"
        @close="toggleDialog"
        @submit="handleSubmit"
    >
        <c-card elevation="0" variant="text">
            <c-form>
                <!-- Hidden username field BEFORE password fields -->
                <!-- This is to ensure that the browser does not autofill the password fields with the username -->
                <c-text-field
                    v-show="false"
                    name="username"
                    v-model="username"
                    autocomplete="username"
                    aria-hidden="true"
                />

                <c-row>
                    <c-col md="12" lg="12" xl="12">
                        <c-password-field
                            v-model="form.current_password"
                            label="Current Password"
                            :loading="loading"
                            :error-messages="formErrors.current_password"
                            autocomplete="current-password"
                        />
                    </c-col>
                    <c-col md="12" lg="12" xl="12">
                        <c-password-field
                            v-model="form.new_password"
                            label="New Password"
                            :loading="loading"
                            :error-messages="formErrors.new_password"
                            autocomplete="new-password"
                        />
                    </c-col>
                    <c-col md="12" lg="12" xl="12">
                        <c-password-field
                            v-model="form.confirm_password"
                            label="Confirm Password"
                            :loading="loading"
                            :error-messages="formErrors.confirm_password"
                            autocomplete="new-password"
                        />
                    </c-col>
                </c-row>
            </c-form>
        </c-card>
    </c-dialog>

    <SnackBar ref="snackBarRef" />
</template>

<script setup>
import { usePage, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

import SnackBar from "@/Components/Utilities/SnackBar.vue";

const props = defineProps({
    errors: Object,
    flash: Object,
    can: Array,
});

const dialog = ref(false);
const loading = ref(false);

// set error start
const formErrors = ref({});
watch(
    () => props.errors,
    (newValue) => {
        formErrors.value = Object.assign({}, newValue);
    },
    { deep: true }
);
// set error end

const page = usePage();

const username = computed(() => {
    return page.props.auth.user.username || "";
});

const form = ref({
    current_password: null,
    new_password: null,
    confirm_password: null,
});

const toggleDialog = () => {
    dialog.value = !dialog.value;

    // clear errors when dialog is toggled
    formErrors.value = {};
};

// notification
const snackBarRef = ref(null);
const toggleSnackBar = (message, color) => {
    if (!snackBarRef.value) {
        return;
    }

    snackBarRef.value.showNotification(message, color);
};

const handleSubmit = () => {
    router.post(
        "/change-password",
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
                loading.value = true;
            },
            onFinish: () => {
                loading.value = false;
            },
        }
    );
};

defineExpose({
    toggleDialog,
});
</script>
