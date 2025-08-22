<template>
    <c-menu location="bottom">
        <template v-slot:activator="{ props }">
            <c-btn-icon
                icon="mdi-cog"
                v-bind="props"
                v-tooltip:bottom="'Settings'"
                class="mr-2 ml-3"
            >
            </c-btn-icon>
        </template>

        <c-list>
            <!-- <c-list-item
                v-for="(item, index) in items"
                :key="index"
                :value="index"
            >
                <c-list-item-title>{{ item.title }}</c-list-item-title>
            </c-list-item> -->

            <c-list-item @click="handleChangePassword">
                <c-list-item-title>Change Password</c-list-item-title>
            </c-list-item>

            <c-list-item @click="handleLogout">
                <c-list-item-title>Logout</c-list-item-title>
            </c-list-item>
        </c-list>
    </c-menu>
    <ChangePassword
        :errors="errors"
        :flash="flash"
        :can="can"
        ref="changePasswordRef"
    />
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

import ChangePassword from "@/Components/Pages/System/Auth/ChangePassword.vue";

defineProps({
    errors: Object,
    flash: Object,
    can: Array,
});

// const items = [{ title: "Change Password" }];

const changePasswordRef = ref(null);
const handleChangePassword = () => {
    if (!changePasswordRef.value) {
        console.error("Change Password component is not available.");
        return;
    }

    changePasswordRef.value.toggleDialog();
};

const handleLogout = () => {
    router.post(
        "/logout",
        {},
        {
            onFinish: () => {
                localStorage.removeItem("token");
            },
        }
    );
};
</script>
