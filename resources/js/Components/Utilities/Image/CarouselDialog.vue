<template>
    <v-dialog v-model="dialog" max-width="900">
        <template v-slot:activator="{ props: activatorProps }">
            <c-chip
                prepend-icon="mdi-image"
                class="ma-1"
                variant="tonal"
                v-bind="activatorProps"
                :closable="closable"
            >
                {{ selectedImage.file_name }}
                <template v-if="closable" #close>
                    <delete-image
                        :selectedImage="selectedImage"
                        :attachmentLink="downloadLink"
                    ></delete-image>
                </template>
            </c-chip>
        </template>

        <v-card prepend-icon="mdi-image" :title="currentAttachmentName">
            <v-container fluid fill-height>
                <v-row align="center" justify="center">
                    <v-col cols="12">
                        <v-carousel
                            v-model="carouselIndex"
                            progress="primary"
                            height="600"
                            show-arrows="hover"
                            hide-delimiters
                            enable-keyboard-navigation
                            @keydown.left="navigateCarousel(-1)"
                            @keydown.right="navigateCarousel(1)"
                        >
                            <v-carousel-item
                                v-for="(attachment, index) in attachmentItem"
                                :key="index"
                                cover
                            >
                                <v-img
                                    :height="height"
                                    :src="attachment.file_link"
                                >
                                    <template v-slot:placeholder>
                                        <div
                                            class="d-flex align-center justify-center fill-height"
                                        >
                                            <v-progress-circular
                                                color="primary"
                                                indeterminate
                                            ></v-progress-circular>
                                        </div>
                                    </template>
                                    <template v-slot:error>
                                        <not-found />
                                    </template>
                                </v-img>
                            </v-carousel-item>
                        </v-carousel>
                    </v-col>
                </v-row>
            </v-container>

            <v-card-actions>
                <v-spacer></v-spacer>
                <c-btn-text @click="dialog = false">close</c-btn-text>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, watch } from "vue";
import CChip from "../../Customs/Chips/CChip.vue";
import CBtnText from "../../Customs/Buttons/CBtnText.vue";
import NotFound from "../../Errors/NotFound.vue";
import DeleteImage from "./Components/DeleteImage.vue";

const props = defineProps({
    width: {
        default: "800",
        type: String,
    },
    height: {
        default: "600",
        type: String,
    },
    selectedImage: {
        default: () => ({
            id: null,
            file_name: "Image",
            file_path: null,
            file_type: null,
            file_link: null,
        }),
        type: Object,
    },
    downloadLink: String,
    closable: {
        default: false,
        type: Boolean,
    },
    attachmentItem: {
        type: Object,
        required: true,
    },
});

const dialog = ref(false);
const carouselIndex = ref(0);
const currentAttachmentName = ref(""); // Ref to store the current attachment name

watch(dialog, (newDialogValue) => {
    if (newDialogValue) {
        // Find the index of the selectedImage
        const index = props.attachmentItem.findIndex(
            (attachment) => attachment.id === props.selectedImage.id
        );
        carouselIndex.value = index !== -1 ? index : 0;
        // Set the initial name
        currentAttachmentName.value = props.selectedImage.file_name;
    } else {
        carouselIndex.value = 0;
        currentAttachmentName.value = "";
    }
});

const navigateCarousel = (direction) => {
    if (props.attachmentItem && props.attachmentItem.length > 0) {
        carouselIndex.value =
            (carouselIndex.value + direction + props.attachmentItem.length) %
            props.attachmentItem.length;
    }
};

// Watch for changes in the carousel index to update the dialog title
watch(carouselIndex, (newIndex) => {
    if (props.attachmentItem && props.attachmentItem.length > 0) {
        currentAttachmentName.value = props.attachmentItem[newIndex].file_name;
    }
});
</script>
