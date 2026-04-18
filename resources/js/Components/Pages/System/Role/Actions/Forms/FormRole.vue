<template>
    <c-form>
        <c-row>
            <c-col>
                <c-text-field
                    label="Name"
                    v-model="form.name"
                    :error-messages="formErrors.name"
                />
            </c-col>
            <c-col>
                <user-group-field
                    :user_groups="user_groups"
                    v-model="form.user_group_id"
                    :errors="formErrors"
                />
            </c-col>
            <c-col md="12" lg="12" xl="12">
                <c-textarea
                    label="Description"
                    v-model="form.description"
                    :error-messages="formErrors.description"
                />
            </c-col>
        </c-row>
    </c-form>
</template>

<script setup>
import { ref, watch } from "vue";

import UserGroupField from "./Components/UserGroupField.vue";

const props = defineProps({
    role: Object,
    user_groups: Array,
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
});

watch(
    () => props.role,
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
    { immediate: true, deep: true },
);

// set error start
const formErrors = ref({});
watch(
    () => props.errors,
    (newValue) => {
        formErrors.value = Object.assign({}, newValue);
    },
    { deep: true },
);
// set error end

const emits = defineEmits(["formValues"]);
const emitFormData = () => {
    emits("formValues", form.value);
};

defineExpose({
    emitFormData,
});
</script>
