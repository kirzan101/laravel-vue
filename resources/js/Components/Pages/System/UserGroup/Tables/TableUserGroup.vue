<template>
    <c-data-table-server
        :headers="headers"
        :hover="true"
        module="user-groups"
        ref="tableDataRef"
    >
        <template #item.name="{ item }">
            <c-btn-edit :label="item.name" />
        </template>
    </c-data-table-server>
</template>

<script setup>
import { ref } from "vue";

import CDataTableServer from "@/Components/Customs/Tables/CDataTableServer.vue";
import CBtnEdit from "@/Components/Customs/Buttons/CBtnEdit.vue";

// Define props
const props = defineProps({
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
