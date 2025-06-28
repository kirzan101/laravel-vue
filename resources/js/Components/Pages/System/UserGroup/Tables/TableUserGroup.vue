<template>
    <c-data-table-server
        :headers="headers"
        :hover="true"
        module="user-groups"
        ref="tableDataRef"
    >
        <template #item.name="{ item }">
            <EditUserGroup
                :userGroup="item"
                :permissions="permissions"
                :user_group_types="user_group_types"
                :modules="modules"
                :errors="errors"
                :flash="flash"
                :can="can"
                ref="editUserGroupRef"
            />
        </template>
    </c-data-table-server>
</template>

<script setup>
import { ref } from "vue";

import EditUserGroup from "../Actions/EditUserGroup.vue";

// Define props
const props = defineProps({
    permissions: Array,
    user_group_types: Array,
    modules: Array,
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
        title: "Code",
        align: "start",
        sortable: false,
        key: "code",
    },
    {
        title: "Description",
        align: "start",
        sortable: false,
        key: "description",
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
