<template>
    <v-empty-state
        :headline="`Whoops, ${code}`"
        :title="title"
        :text="text"
        :image="computedImage"
    >
    </v-empty-state>
</template>

<script setup>
defineProps({
    code: {
        type: Number,
        default: 500,
    },
    title: {
        type: String,
        default: "Server Error",
    },
    text: {
        type: String,
        default: "Something went wrong. Please try again later.",
    },
});

const computedImage = computed(() => {
    const defaultErrors = [401, 404, 500];
    if (defaultErrors.includes(props.code)) {
        return `/images/errors/error_${props.code}.svg`;
    }

    return `/images/errors/error_500.svg`;
});
</script>
