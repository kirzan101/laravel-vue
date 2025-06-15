<template>
    <c-form>
        <c-row>
            <c-col>
                <c-text-field label="Name" v-model="form.name" />
            </c-col>
            <c-col>
                <c-text-field label="Code" v-model="form.code" />
            </c-col>
            <c-col md="12" lg="12" xl="12">
                <c-textarea label="Description" v-model="form.description" />
            </c-col>
        </c-row>
    </c-form>
</template>

<script setup>
import { ref, watch } from "vue";

// custom components
import CForm from "@/Components/Customs/Forms/CForm.vue";
import CRow from "@/Components/Customs/Grids/CRow.vue";
import CCol from "@/Components/Customs/Grids/CCol.vue";
import CTextField from "@/Components/Customs/Inputs/CTextField.vue";
import CTextarea from "@/Components/Customs/Textareas/CTextarea.vue";

const props = defineProps({
    userGroup: Object,
    errors: Object,
    flsash: Object,
    can: Array,
});

const form = ref({
    name: null,
    code: null,
    description: null,
    permissions: [],
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
</script>
