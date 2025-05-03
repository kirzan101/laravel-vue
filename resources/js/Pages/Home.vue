<template>
    <Head :title="pageTitle" />
    <main-layout>
        <v-container>
            <h1>Laravel - Vue3</h1>
            <div>
                <c-btn color="secondary">Button</c-btn>
                <c-btn-tonal color="secondary">Label</c-btn-tonal>
                <c-btn-submit color="secondary" />
                <c-btn-text color="secondary">close</c-btn-text>
                <c-btn-edit color="secondary" />
                <c-btn-add color="secondary" />
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
