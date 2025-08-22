<template>
    <c-data-table-server
        :headers="headers"
        :hover="true"
        module="profiles"
        ref="tableDataRef"
    >
        <template #item.name="{ item }">
            <EditProfile
                :profile="item"
                :user_groups="user_groups"
                :account_types="account_types"
                :errors="errors"
                :flash="flash"
                :can="can"
                ref="editProfileRef"
            />
        </template>
        <template #item.last_login_at="{ item }">
            {{ item.last_login_at ?? "-" }}
        </template>
        <template #item.actions="{ item }">
            <ResetPassword
                :profile="item"
                :can="can"
                :flash="flash"
                :errors="errors"
            />
            <SetAccountStatus
                :profile="item"
                :can="can"
                :flash="flash"
                :errors="errors"
            />
        </template>
    </c-data-table-server>
</template>

<script setup>
import { ref } from "vue";

import EditProfile from "../Actions/EditProfile.vue";
import ResetPassword from "../Actions/ResetPassword.vue";
import SetAccountStatus from "../Actions/SetAccountStatus.vue";

// Define props
const props = defineProps({
    user_groups: Array,
    account_types: Array,
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
        title: "Username",
        align: "start",
        sortable: false,
        key: "username",
    },
    {
        title: "Email",
        align: "start",
        sortable: false,
        key: "email",
    },
    {
        title: "Last Login",
        align: "start",
        sortable: false,
        key: "last_login_at",
    },
    {
        title: "Created At",
        align: "start",
        sortable: false,
        key: "created_at",
    },
    {
        title: "",
        align: "start",
        sortable: false,
        key: "actions",
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
