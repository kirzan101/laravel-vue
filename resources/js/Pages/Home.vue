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
                    <c-date-field v-model="dateInput" />
                    <c-date-range-field
                        v-model="dateRangeInput"
                        @dateRangeValues="getDateRangeValue"
                    />
                    {{ dateRangeInputValue }}
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
                <div>
                    <c-chip-file fileType="jpeg" color="primary"
                        >Img</c-chip-file
                    >
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

import SnackBar from "@/Components/Utilities/SnackBar.vue";
import CopyText from "@/Components/Utilities/CopyText.vue";

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
const dateInput = ref(null);
const dateRangeInput = ref(null);
// const dateRangeInput = ref(["2025-05-05", "2025-05-10"]);
const dateRangeInputValue = ref(null);

const getDateRangeValue = (value) => {
    if (Array.isArray(value) && value.length > 0) {
        dateRangeInputValue.value = value;
    }
};

const snackBarRef = ref(null);
const toggleSnackBar = () => {
    if (!snackBarRef.value) {
        return;
    }

    snackBarRef.value.toggleNotification();
};
</script>
