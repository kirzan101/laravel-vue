<template>
    <c-data-table-server
        :headers="headers"
        :hover="true"
        module="roles"
        ref="tableDataRef"
    >
        <template #item.name="{ item }">
            <EditRole
                :role="item"
                :permissions="permissions"
                :user_groups="user_groups"
                :moduleLists="moduleLists"
                :errors="errors"
                :flash="flash"
                :can="can"
                ref="editRoleRef"
            />
        </template>
        <template #item.is_active="{ item }">
            <ActiveChip :is_active="item.is_active" />
        </template>
    </c-data-table-server>
</template>

<script setup>
import { ref } from "vue";

import EditRole from "../Actions/EditRole.vue";
import ActiveChip from "./Components/ActiveChip.vue";

// Define props
const props = defineProps({
    permissions: Array,
    user_groups: Array,
    moduleLists: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

const headers = ref([
    {
        title: "Name",
        align: "start",
        sortable: false,
        key: "name",
    },
    {
        title: "Description",
        align: "start",
        sortable: false,
        key: "description",
    },
    {
        title: "Is Active",
        align: "start",
        sortable: false,
        key: "is_active",
    },
]);

const tableDataRef = ref(null);
const toggleLoadData = (value = {}) => {
    if (tableDataRef.value) {
        tableDataRef.value.loadData(value);
    }
};

defineExpose({
    toggleLoadData,
});
</script>
