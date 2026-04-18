<template>
    <c-container>
        <c-breadcrumbs :items="['Home', 'Roles']"></c-breadcrumbs>
        <c-card>
            <c-row class="mt-1" justify="space-between">
                <c-col cols="12" sm="4">
                    <c-card-title>
                        Roles
                        <AddRole
                            :permissions="permissions"
                            :user_groups="user_groups"
                            :modules="modules"
                            :showBtn="showAddBtn"
                            :errors="errors"
                            :flash="flash"
                            :can="can"
                            ref="addRoleRef"
                        />
                        <c-fab-lower-right
                            v-if="!showAddBtn"
                            icon="mdi-plus"
                            @click="toggleAddRoleDialog"
                        />
                    </c-card-title>
                </c-col>
                <c-col cols="12" sm="4">
                    <c-search-field v-model="filters.search" clearable />
                </c-col>
            </c-row>

            <TableRole
                :permissions="permissions"
                :user_groups="user_groups"
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

import AddRole from "./Actions/AddRole.vue";
import TableRole from "./Tables/TableRole.vue";

const props = defineProps({
    permissions: Array,
    user_groups: Array,
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
    { deep: true },
);

// reload data when flash message changes
watch(
    () => props.flash,
    () => {
        toggleLoadData(filters.value);
    },
    { immediate: true },
);

const { mobile } = useDisplay();

// show normal add button on desktop and hide on mobile
const showAddBtn = ref(true);
watch(
    () => mobile.value,
    (newVal) => {
        showAddBtn.value = !newVal; // Hide the button on mobile
    },
    { immediate: true },
);

const addRoleRef = ref(null);
const toggleAddRoleDialog = () => {
    if (addRoleRef.value) {
        addRoleRef.value.toggleDialog();
    }
};
</script>
