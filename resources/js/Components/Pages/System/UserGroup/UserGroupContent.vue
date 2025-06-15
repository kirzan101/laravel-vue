<template>
    <c-container>
        <c-card>
            <c-row class="mt-1" justify="space-between">
                <c-col cols="12" sm="4">
                    <p class="text-h6">
                        User Groups
                        <c-btn-add />
                    </p>
                </c-col>
                <v-col cols="12" sm="4">
                    <c-search-field v-model="search" />
                </v-col>
            </c-row>
            <TableUserGroup
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
import { ref } from "vue";

// custom components
import CContainer from "@/Components/Customs/Containers/CContainer.vue";
import CCard from "@/Components/Customs/Cards/CCard.vue";
import CRow from "@/Components/Customs/Grids/CRow.vue";
import CCol from "@/Components/Customs/Grids/CCol.vue";
import CBtnAdd from "@/Components/Customs/Buttons/CBtnAdd.vue";
import CSearchField from "@/Components/Customs/Inputs/CSearchField.vue";

import TableUserGroup from "./Tables/TableUserGroup.vue";

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
const search = ref(null);
useDebouncedWatch(search, (val) => {
    toggleLoadData({ search: val });
});
</script>
