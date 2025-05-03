<template>
    <Head :title="pageTitle" />
    <main-layout>
        <v-container>
            <h1>Laravel - Vue3</h1>
            <div>
                <c-alert-notice text="The quick brown fox jumps over the lazy dog." />
                <c-alert-error text="The quick brown fox jumps over the lazy dog." />
                <c-alert-success text="The quick brown fox jumps over the lazy dog." />
            </div>
            <div>
                <c-btn>Button</c-btn>
                <c-btn-tonal>Label</c-btn-tonal>
                <c-btn-submit />
                <c-btn-text>close</c-btn-text>
                <c-btn-edit />
                <c-btn-add />
            </div>
        </v-container>
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
import { computed } from "vue";

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
</script>
