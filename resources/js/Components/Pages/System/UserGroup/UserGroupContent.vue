<template>
    <c-container>
        <c-breadcrumbs :items="['Home', 'User Groups']"></c-breadcrumbs>
        <c-card>
            <c-row class="mt-1" justify="space-between">
                <c-col cols="12" sm="4">
                    <p class="text-h6 d-flex flex-column flex-sm-row">
                        User Groups
                        <AddUserGroup
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
                    </p>
                </c-col>
                <v-col cols="12" sm="4">
                    <c-search-field v-model="filters.search" />
                </v-col>
            </c-row>
            <TableUserGroup
                :errors="errors"
                :flash="flash"
                :can="can"
                ref="tableDataRef"
            />
        </c-card>
        <!-- Uncomment the following line to enable the floating action button -->
        <!-- <c-fab-lower-right icon="mdi-plus" /> -->
    </c-container>
</template>

<script setup>
import { useDebouncedWatch } from "@/Composables/useDebouncedWatch";
import { ref, onMounted, watch } from "vue";
import { useDisplay } from "vuetify";

// custom components
import CContainer from "@/Components/Customs/Containers/CContainer.vue";
import CCard from "@/Components/Customs/Cards/CCard.vue";
import CRow from "@/Components/Customs/Grids/CRow.vue";
import CCol from "@/Components/Customs/Grids/CCol.vue";
import CSearchField from "@/Components/Customs/Inputs/CSearchField.vue";
import CBreadcrumbs from "@/Components/Customs/Breadcrumbs/CBreadcrumbs.vue";
import CFabLowerRight from "@/Components/Customs/Fabs/CFabLowerRight.vue";

import TableUserGroup from "./Tables/TableUserGroup.vue";
import AddUserGroup from "./Actions/AddUserGroup.vue";

// Define props
const props = defineProps({
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

//add form
const { mobile } = useDisplay();

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
