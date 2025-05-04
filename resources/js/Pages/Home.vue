<template>
    <Head :title="pageTitle" />
    <main-layout>
        <v-container>
            <c-card>
                <h1>Laravel - Vue3</h1>
                <div>
                    <c-text-field label="Test" />
                    <c-text-field-solo placeholder="Test solo" />
                    <c-search-field v-model="search" />
                    <c-number-field v-model="numberInput" />
                    <c-decimal-field v-model="decimalInput" />
                </div>
                <div>
                    <c-alert-notice
                        text="The quick brown fox jumps over the lazy dog."
                    />
                    <c-alert-error
                        text="The quick brown fox jumps over the lazy dog."
                    />
                    <c-alert-success
                        text="The quick brown fox jumps over the lazy dog."
                    />
                    <c-alert-system-error
                        text="The quick brown fox jumps over the lazy dog."
                    />
                </div>
                <div>
                    <c-btn>Button</c-btn>
                    <c-btn-tonal>Label</c-btn-tonal>
                    <c-btn-submit />
                    <c-btn-text>close</c-btn-text>
                    <c-btn-edit />
                    <c-btn-add />
                    <c-btn @click="toggleSnackBar">Notification</c-btn>
                    <copy-text textValue="Lalatina" />
                </div>
            </c-card>
        </v-container>
        <snack-bar ref="snackBarRef" />
    </main-layout>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";

import BlankLayout from "../Layouts/BlankLayout.vue";
import MainLayout from "../Layouts/MainLayout.vue";
import CBtn from "../Components/Customs/Buttons/CBtn.vue";
import CBtnTonal from "../Components/Customs/Buttons/CBtnTonal.vue";
import CBtnSubmit from "../Components/Customs/Buttons/CBtnSubmit.vue";
import CBtnText from "../Components/Customs/Buttons/CBtnText.vue";
import CBtnEdit from "../Components/Customs/Buttons/CBtnEdit.vue";
import CBtnAdd from "../Components/Customs/Buttons/CBtnAdd.vue";
import CAlertNotice from "../Components/Customs/Alerts/CAlertNotice.vue";
import CAlertError from "../Components/Customs/Alerts/CAlertError.vue";
import CAlertSuccess from "../Components/Customs/Alerts/CAlertSuccess.vue";
import CAlertSystemError from "../Components/Customs/Alerts/CAlertSystemError.vue";
import CTextField from "../Components/Customs/Inputs/CTextField.vue";
import CTextFieldSolo from "../Components/Customs/Inputs/CTextFieldSolo.vue";
import CSearchField from "../Components/Customs/Inputs/CSearchField.vue";
import CNumberField from "../Components/Customs/Inputs/CNumberField.vue";
import CDecimalField from "../Components/Customs/Inputs/CDecimalField.vue";
import CCard from "../Components/Customs/Cards/CCard.vue";
import Loading from "../Components/utilities/Loading.vue";
import SnackBar from "../Components/utilities/SnackBar.vue";
import CopyText from "../Components/utilities/CopyText.vue";
import { computed, ref } from "vue";

const props = defineProps({
    errors: Object,
    flash: Object,
});

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});

const pageTitle = computed(() => {
    if (!appName.value) {
        return "Home";
    }

    return `${appName.value} | Home`;
});

const search = ref(null);
const numberInput = ref(null);
const decimalInput = ref(null);

const snackBarRef = ref(null);
const toggleSnackBar = () => {
    if (!snackBarRef.value) {
        return;
    }

    snackBarRef.value.toggleNotification();
};
</script>
