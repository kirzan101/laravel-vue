<template>
    <v-navigation-drawer v-model="drawer" app>
        <v-sheet class="pa-4" color="primary">
            <c-avatar class="mb-4" color="blue-grey-lighten-2" size="53">
                <c-icon :icon="profileIcon" size="x-large"></c-icon>
            </c-avatar>
            <div>{{ fullName }}</div>
            <div class="text-caption">{{ emailAddress }}</div>
        </v-sheet>

        <v-divider></v-divider>

        <v-list nav>
            <v-list-item title="Navigation drawer" link></v-list-item>
            <v-list-item href="/user-groups" @click.prevent>
                <v-list-item-title @click="router.visit('/user-groups')"
                    >User group
                </v-list-item-title>
            </v-list-item>
        </v-list>

        <template v-slot:append>
            <v-footer class="d-flex flex-column text-center">
                <div>
                    <strong>{{ appName }}</strong>
                    <br />
                    <strong>Developer ©</strong> —
                    {{ new Date().getFullYear() }}
                </div>
            </v-footer>
        </template>
    </v-navigation-drawer>

    <nav-bar
        :hasDrawer="true"
        :errors="errors"
        :flash="flash"
        :can="can"
        @toggleDrawer="toggleDrawer"
    />
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import NavBar from "./NavBar.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import { useDisplay } from "vuetify";

defineProps({
    errors: Object,
    flash: Object,
    can: Array,
});

const drawer = ref(false);

const { mobile } = useDisplay();
onMounted(() => {
    if (!mobile.value) {
        drawer.value = true;
    }
});

const toggleDrawer = () => {
    drawer.value = !drawer.value;
};

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});

const fullName = computed(() => {
    return page.props.auth.user.name ?? "Full Name";
});

const emailAddress = computed(() => {
    return page.props.auth.user.email ?? "Email Address";
});

const profileIcon = computed(() => {
    return page.props.auth.user.isAdmin ? "mdi-account-star" : "mdi-account";
});
</script>
