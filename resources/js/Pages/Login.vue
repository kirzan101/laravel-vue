<template>
    <Head :title="`Login — ${appName}`" />
    <empty-layout>
        <c-row fluid>
            <c-col
                style="height: 100vh"
                cols="12"
                sm="12"
                md="6"
                lg="6"
                class="hidden-sm hidden-xs"
            >
                <c-container
                    pa-0
                    class="bg-primary d-flex justify-center align-center flex-column"
                    style="height: 100vh"
                >
                    <c-container class="d-flex justify-center align-center">
                        <!-- <v-img
                            class=""
                            max-height="350"
                            max-width="350"
                            src="images/error_500.svg"
                        ></v-img> -->
                    </c-container>
                    <p class="text-h3 font-weight-medium">
                        {{ appName }}
                    </p>
                </c-container>
            </c-col>
            <c-col
                cols="12"
                sm="12"
                md="6"
                lg="6"
                class="pa-10 overflow-hidden"
            >
                <c-container pa-0>
                    <c-container
                        class="d-lg-none d-md-none d-flex justify-center align-center flex-column"
                    >
                        <c-container
                            class="pa-0 d-flex justify-center align-center"
                        >
                            <!-- <v-img
                                max-height="150"
                                max-width="150"
                                src="images/error_500.svg"
                            ></v-img> -->
                        </c-container>
                        <p class="py-4 text-h4">{{ appName }}</p>
                    </c-container>
                    <h1 class="my-6 text-h5">Login</h1>
                    <c-form @submit.prevent="handleFormSubmission">
                        <c-alert-system-error
                            v-if="props.errors.error"
                            class="mb-5"
                        >
                            {{ props.errors.error }}
                        </c-alert-system-error>

                        <c-row>
                            <c-col cols="12" sm="12" md="12" lg="12" xl="12">
                                <c-text-field
                                    v-model="form.username"
                                    :loading="btnDisabled"
                                    label="Username"
                                    class="mt-2"
                                    variant="outlined"
                                    autocomplete="username"
                                />
                            </c-col>

                            <c-col cols="12" sm="12" md="12" lg="12" xl="12">
                                <c-password-field
                                    v-model="form.password"
                                    :loading="btnDisabled"
                                    class="mt-2"
                                    variant="outlined"
                                    autocomplete="pasword"
                                />
                            </c-col>
                        </c-row>

                        <c-btn-submit
                            prepend-icon="mdi-login-variant"
                            :loading="btnDisabled"
                            class="my-2"
                            label="Login"
                            size="default"
                        />
                    </c-form>
                    <c-footer
                        class="bg-transparent d-flex flex-column text-center mt-10"
                    >
                        <div>
                            <strong>{{ `${appName} v${appVersion}` }}</strong>
                            <br />
                            <strong>Developer ©</strong> —
                            {{ new Date().getFullYear() }}
                        </div>
                    </c-footer>
                </c-container>
            </c-col>
        </c-row>
        <c-fab-lower-right
            @click="isDarkMode = !isDarkMode"
            :icon="isDarkMode ? 'mdi-weather-sunny' : 'mdi-weather-night'"
            style="position: fixed; top: 400px; right: 25px; z-index: 1000"
        />
    </empty-layout>
</template>
<script setup>
import { router, Head, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch } from "vue";
import { useTheme } from "vuetify";

// layout
import EmptyLayout from "@/Layouts/EmptyLayout.vue";

const props = defineProps({
    flash: Object,
    errors: Object,
});

const form = ref({
    username: null,
    password: null,
});

const visible = ref(false);

const page = usePage();

const appName = computed(() => {
    return page.props.appName ?? "App Name";
});

const appVersion = computed(() => {
    return page.props.appVersion;
});

const theme = useTheme();
const isDarkMode = ref(false);

// Sync Vuetify theme and localStorage based on isDarkMode
watch(isDarkMode, (enabled) => {
    const newTheme = enabled ? "dark" : "light";
    theme.global.name.value = newTheme;
    localStorage.setItem("theme", newTheme);
});

// On mount, read theme from localStorage and apply
onMounted(() => {
    const savedTheme = localStorage.getItem("theme") || "light";
    theme.global.name.value = savedTheme;
    isDarkMode.value = savedTheme === "dark";
});

const btnDisabled = ref(false);
const handleFormSubmission = () => {
    router.post("/login", form.value, {
        onSuccess: ({ props }) => {
            localStorage.setItem("token", props.token);
        },
        onError: () => {
            //
        },
        onBefore: () => {
            btnDisabled.value = true;
        },
        onFinish: () => {
            btnDisabled.value = false;
        },
    });
};
</script>
