<template>
    <v-snackbar
        v-model="snackBar"
        :color="props.color"
        :timeout="props.timeout"
    >
        {{ props.message }}

        <template v-slot:actions="{ attrs }">
            <v-hover v-bind="attrs">
                <template v-slot:default="{ isHovering, props }">
                    <span v-bind="props">
                        <v-btn
                            v-if="isHovering"
                            color="white"
                            variant="text"
                            @click="toggleNotification(false)"
                            icon="mdi-close-circle"
                        >
                        </v-btn>

                        <v-progress-circular
                            v-else
                            color="white"
                            :size="20"
                            :width="2"
                            class="mr-3"
                            indeterminate
                        >
                            <template v-slot:default>
                                <span style="font-size: 10px">{{
                                    countdown
                                }}</span>
                            </template>
                        </v-progress-circular></span
                    >
                </template>
            </v-hover>
        </template>
    </v-snackbar>
</template>

<script setup>
import { ref, watch, onUnmounted } from "vue";

const props = defineProps({
    message: {
        type: String,
        default: "Notification?",
    },
    timeout: {
        type: Number,
        default: 3000,
    },
    color: {
        type: String,
        default: "primary",
    },
});

const snackBar = ref(false);
const countdown = ref(Math.ceil(props.timeout / 1000));
let timer = null;

const startCountdown = () => {
    countdown.value = Math.ceil(props.timeout / 1000);
    clearInterval(timer);
    timer = setInterval(() => {
        if (countdown.value > 0) {
            countdown.value--;
        }
        if (countdown.value <= 0) {
            clearInterval(timer);
            toggleNotification(false);
        }
    }, 1000);
};

watch(snackBar, (val) => {
    if (val) {
        startCountdown();
    } else {
        clearInterval(timer);
    }
});

onUnmounted(() => {
    clearInterval(timer);
});

const toggleNotification = (value = true) => {
    snackBar.value = value;
};

defineExpose({
    toggleNotification,
});
</script>
