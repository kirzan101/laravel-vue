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
                <c-select
                    label="Code"
                    :items="user_group_types"
                    v-model="form.code"
                    :error-messages="formErrors.code"
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

const props = defineProps({
    userGroup: Object,
    user_group_types: Array,
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

watch(
    () => props.userGroup,
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

const emits = defineEmits(["formValues"]);
const emitFormData = () => {
    emits("formValues", form.value);
};

defineExpose({
    emitFormData,
});
</script>
