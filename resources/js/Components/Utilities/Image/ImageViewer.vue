<!--
  Purpose:
  A dynamic image viewer component that intelligently switches between a single image dialog
  and a carousel dialog for multiple images. Useful for viewing attachments, previews, or
  media galleries in a clean Vuetify-based modal format.

  Usage Example:

  <image-viewer
      :attachments="attachmentsArray"
      :selectedAttachment="currentAttachment"
      :attachmentLink="linkToAttachment"
      :closable="true"
      :width="800"
      :height="600"
  />

  Props: 
    - attachments: Array of attachment objects (required) 
    - selectedAttachment: Object (optional) – the currently selected attachment
    - attachmentLink: String (optional) – URL or path to the image source
    - closable: Boolean (default: true) – whether the dialog can be closed
    - width: String | Number (optional) – custom width for the dialog
    - height: String | Number (optional) – custom height for the dialog
-->

<template>
    <carousel-dialog
        v-if="attachments.length > 1"
        :attachments="attachments"
        :attachmentLink="attachmentLink"
        :selectedAttachment="selectedAttachment"
        :closable="closable"
        :width="width"
        :height="height"
    />
    <image-dialog
        v-else
        :attachmentLink="attachmentLink"
        :selectedAttachment="selectedAttachment"
        :closable="closable"
        :width="width"
        :height="height"
    />
</template>

<script setup>
import ImageDialog from "./ImageDialog.vue";
import CarouselDialog from "./CarouselDialog.vue";

const props = defineProps({
    attachments: {
        type: Array,
        required: true,
    },
    selectedAttachment: {
        type: Object,
        default: () => ({}),
    },
    attachmentLink: {
        type: String,
        default: "",
    },
    closable: {
        type: Boolean,
        default: true,
    },
    width: {
        type: [String, Number],
        default: "auto",
    },
    height: {
        type: [String, Number],
        default: "auto",
    },
});
</script>
