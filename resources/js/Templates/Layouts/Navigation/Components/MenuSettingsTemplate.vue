<template>
    <v-menu location="bottom">
        <template v-slot:activator="{ props }">
            <v-btn
                icon="mdi-cog"
                v-bind="props"
                v-tooltip:bottom="'Settings'"
            >
            </v-btn>
        </template>

        <v-list>
            <v-list-item
                v-for="(item, index) in items"
                :key="index"
                :value="index"
            >
                <v-list-item-title>{{ item.title }}</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script setup>
const items = [{ title: "Change Password" }, { title: "Logout" }];
</script>
