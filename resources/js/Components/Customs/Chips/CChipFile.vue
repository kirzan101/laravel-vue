<template>
    <c-chip :prepend-icon="computedFileTypeIcon" v-bind="$attrs">
        <v-slot />
    </c-chip>
</template>

<script setup>
import CChip from "./CChip.vue";

const props = defineProps({
    fileType: String,
});

const computedFileTypeIcon = computed(() => {
    if (
        props.fileType === "png" ||
        props.fileType === "jpg" ||
        props.fileType === "jpeg"
    ) {
        return "mdi-image";
    }

    if (props.fileType === "pdf") {
        return "mdi-file-pdf";
    }

    if (props.fileType === "doc" || props.fileType === "docx") {
        return "mdi-file-word";
    }

    if (props.fileType === "xls" || props.fileType === "xlsx") {
        return "mdi-file-excel";
    }

    if (props.fileType === "ppt" || props.fileType === "pptx") {
        return "mdi-file-powerpoint";
    }
    if (props.fileType === "zip" || props.fileType === "rar") {
        return "mdi-file-archive";
    }

    return "mdi-file";
});
</script>
