<template>
    <c-form>
        <c-row>
            <c-col md="4" lg="4" xl="4" xxl="4">
                <c-text-field
                    label="First Name"
                    v-model="form.first_name"
                    :error-messages="formErrors.first_name"
                />
            </c-col>
            <c-col md="4" lg="4" xl="4" xxl="4">
                <c-text-field
                    label="Middle Name"
                    v-model="form.middle_name"
                    :error-messages="formErrors.middle_name"
                />
            </c-col>
            <c-col md="4" lg="4" xl="4" xxl="4">
                <c-text-field
                    label="Last Name"
                    v-model="form.last_name"
                    :error-messages="formErrors.last_name"
                />
            </c-col>
            <c-col md="4" lg="4" xl="4" xxl="4">
                <c-text-field
                    label="Nickname"
                    v-model="form.nickname"
                    :error-messages="formErrors.nickname"
                />
            </c-col>
            <c-col md="4" lg="4" xl="4" xxl="4">
                <AccountTypeSelect
                    v-model="form.type"
                    :account_types="account_types"
                    :error-messages="formErrors.type"
                />
            </c-col>
            <c-col md="4" lg="4" xl="4" xxl="4">
                <UserGroupSelect
                    v-model="form.user_group_id"
                    :user_groups="user_groups"
                    :error-messages="formErrors.user_group_id"
                />
            </c-col>
            <c-col md="3" lg="3" xl="3" xxl="3">
                <c-text-field
                    label="Username"
                    v-model="form.username"
                    :error-messages="formErrors.username"
                />
            </c-col>
            <c-col md="4" lg="4" xl="4" xxl="4">
                <c-text-field
                    label="Email"
                    v-model="form.email"
                    :error-messages="formErrors.email"
                />
            </c-col>

            <c-col md="5" lg="5" xl="5" xxl="5">
                <ContactNumbersField
                    :contactNumbers="form.contact_numbers"
                    :error-messages="formErrors.contact_numbers"
                    :max-input="3"
                    @filteredContactNo="getContactNumbers"
                />
            </c-col>

            <c-col>
                <UserGroupField
                    v-model="form.user_group_id"
                    :error-messages="formErrors.user_group_id"
                />
            </c-col>
        </c-row>
    </c-form>
</template>

<script setup>
import { ref, watch } from "vue";

import UserGroupSelect from "./Components/UserGroupSelect.vue";
import ContactNumbersField from "@/Components/Utilities/ContactNumbersField.vue";
import AccountTypeSelect from "./Components/AccountTypeSelect.vue";
import UserGroupField from "@/Components/Utilities/SearchBoxes/UserGroupField.vue";

const props = defineProps({
    profile: Object,
    user_groups: Array,
    account_types: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const form = ref({
    id: null,
    first_name: null,
    middle_name: null,
    last_name: null,
    nickname: null,
    type: null,
    contact_numbers: [],
    username: null,
    email: null,
    user_group_id: null,
    user_id: null,
});

watch(
    () => props.profile,
    (newVal) => {
        if (newVal && Object.keys(newVal).length > 0) {
            Object.keys(form.value).forEach((key) => {
                const isNull = form.value[key] === null;
                const isEmptyArray =
                    Array.isArray(form.value[key]) &&
                    form.value[key].length === 0;

                // Only update if the current value is null or an empty array
                if (isNull || isEmptyArray) {
                    if (newVal.hasOwnProperty(key)) {
                        form.value[key] = newVal[key];
                    }
                }
            });
        }
    },
    { immediate: true, deep: true }
);

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

const getContactNumbers = (inputs) => {
    form.value.contact_numbers = inputs;
};

const emits = defineEmits(["formValues"]);
const emitFormData = () => {
    emits("formValues", form.value);
};

defineExpose({
    emitFormData,
});
</script>
