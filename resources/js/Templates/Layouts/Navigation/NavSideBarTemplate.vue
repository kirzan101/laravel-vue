<template>
    <v-navigation-drawer v-model="drawer" app>
        <v-sheet class="pa-4" color="primary">
            <v-avatar class="mb-4" color="blue-grey-lighten-2" size="53">
                <v-icon icon="mdi-account-star" size="x-large"></v-icon>
            </v-avatar>
            <div>Lalatina Ford</div>
            <div class="text-caption">lalatina@mail.com</div>
        </v-sheet>

        <v-divider></v-divider>

        <v-list nav>
            <v-list-item title="Navigation drawer" link></v-list-item>
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

    <nav-bar :hasDrawer="true" @toggleDrawer="toggleDrawer" />
</template>

<script setup>
import { computed, ref } from "vue";
import NavBar from "./NavBarTemplate.vue";
import { usePage } from "@inertiajs/vue3";

const drawer = ref(false);

const toggleDrawer = () => {
    drawer.value = !drawer.value;
};

const page = usePage();
const appName = computed(() => {
    return page.props.appName ?? "App Name";
});
</script>
