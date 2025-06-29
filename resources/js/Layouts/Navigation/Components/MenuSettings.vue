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
            <c-list-item
                v-for="(item, index) in items"
                :key="index"
                :value="index"
            >
                <c-list-item-title>{{ item.title }}</c-list-item-title>
            </c-list-item>

            <c-list-item @click="handleLogout">
                <c-list-item-title>Logout</c-list-item-title>
            </c-list-item>
        </c-list>
    </c-menu>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
const items = [{ title: "Change Password" }];

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
