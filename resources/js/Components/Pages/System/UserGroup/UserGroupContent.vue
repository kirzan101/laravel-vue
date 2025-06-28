<template>
    <c-container>
        <c-breadcrumbs :items="['Home', 'User Groups']"></c-breadcrumbs>
        <c-card>
            <c-row class="mt-1" justify="space-between">
                <c-col cols="12" sm="4">
                    <v-card-title>
                        User Groups
                        <AddUserGroup
                            :permissions="permissions"
                            :user_group_types="user_group_types"
                            :modules="modules"
                            :showBtn="showAddBtn"
                            :errors="errors"
                            :flash="flash"
                            :can="can"
                            ref="addUserGroupRef"
                        />
                        <c-fab-lower-right
                            v-if="!showAddBtn"
                            icon="mdi-plus"
                            @click="toggleAddUserGroupDialog"
                        />
                    </v-card-title>
                </c-col>
                <v-col cols="12" sm="4">
                    <c-search-field v-model="filters.search" clearable />
                </v-col>
            </c-row>
            <TableUserGroup
                :permissions="permissions"
                :user_group_types="user_group_types"
                :modules="modules"
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

import TableUserGroup from "./Tables/TableUserGroup.vue";
import AddUserGroup from "./Actions/AddUserGroup.vue";

// Define props
const props = defineProps({
    permissions: Array,
    user_group_types: Array,
    modules: Array,
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

const addUserGroupRef = ref(null);
const toggleAddUserGroupDialog = () => {
    if (addUserGroupRef.value) {
        addUserGroupRef.value.toggleDialog();
    }
};
</script>
