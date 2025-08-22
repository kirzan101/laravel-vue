<template>
    <c-container>
        <c-breadcrumbs :items="['Home', 'Profiles']"></c-breadcrumbs>
        <c-card>
            <c-row class="mt-1" justify="space-between">
                <c-col cols="12" sm="4">
                    <c-card-title>
                        Profiles
                        <AddProfile
                            :user_groups="user_groups"
                            :account_types="account_types"
                            :showBtn="showAddBtn"
                            :errors="errors"
                            :flash="flash"
                            :can="can"
                            ref="addProfileRef"
                        />
                        <c-fab-lower-right
                            v-if="!showAddBtn"
                            icon="mdi-plus"
                            @click="toggleAddProfileDialog"
                        />
                    </c-card-title>
                </c-col>
                <c-col cols="12" sm="4">
                    <c-search-field v-model="filters.search" clearable />
                </c-col>
            </c-row>
            <TableProfile
                :user_groups="user_groups"
                :account_types="account_types"
                :errors="errors"
                :flash="flash"
                :can="can"
                ref="tableDataRef"
            />
        </c-card>
    </c-container>
</template>

<script setup>
import { useDebouncedWatch } from "@/Composables/useDebouncedWatch";
import { ref, onMounted, watch } from "vue";
import { useDisplay } from "vuetify";

import TableProfile from "./Tables/TableProfile.vue";
import AddProfile from "./Actions/AddProfile.vue";

// Define props
const props = defineProps({
    user_groups: Array,
    account_types: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const tableDataRef = ref(null);
const toggleLoadData = (value = {}) => {
    if (tableDataRef.value) {
        tableDataRef.value.toggleLoadData(value);
    }
};

// handle search input
const filters = ref({
    search: null,
    // Add other filters as needed
});

useDebouncedWatch(
    filters,
    (value) => {
        toggleLoadData(value);
    },
    undefined,
    { deep: true }
);

// reload data when flash message changes
watch(
    () => props.flash,
    () => {
        toggleLoadData(filters.value);
    },
    { immediate: true }
);

const { mobile } = useDisplay();

// show normal add button on desktop and hide on mobile
const showAddBtn = ref(true);
watch(
    () => mobile.value,
    (newVal) => {
        showAddBtn.value = !newVal; // Hide the button on mobile
    },
    { immediate: true }
);

const addProfileRef = ref(null);
const toggleAddProfileDialog = () => {
    if (addProfileRef.value) {
        addProfileRef.value.toggleDialog();
    }
};
</script>
