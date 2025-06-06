<template>
    <v-dialog max-width="900">
        <template v-slot:activator="{ props: activatorProps }">
            <c-chip
                prepend-icon="mdi-image"
                class="ma-1"
                variant="tonal"
                v-bind="activatorProps"
                :closable="closable"
            >
                {{ selectedAttachment.file_name }}
                <template v-if="closable" #close>
                    <delete-image
                        :selectedAttachment="selectedAttachment"
                        :attachmentLink="attachmentLink"
                    ></delete-image>
                </template>
            </c-chip>
        </template>

        <template v-slot:default="{ isActive }">
            <v-card
                prepend-icon="mdi-image"
                :title="selectedAttachment.file_name"
            >
                <v-container fluid fill-height>
                    <v-row align="center" justify="center">
                        <v-col cols="auto">
                            <v-img
                                :width="width"
                                :height="height"
                                :src="selectedAttachment.file_link"
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
                        </v-col>
                    </v-row>
                </v-container>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn text="Close" @click="isActive.value = false"></v-btn>
                </v-card-actions>
            </v-card>
        </template>
    </v-dialog>
</template>

<script setup>
import CChip from "../../Customs/Chips/CChip.vue";
import DeleteImage from "./Components/DeleteImage.vue";
import NotFound from "../../Errors/NotFound.vue";

const props = defineProps({
    width: {
        default: "800",
        type: String,
    },
    height: {
        default: "600",
        type: String,
    },
    selectedAttachment: {
        default: {
            id: null,
            file_name: "Image",
            file_path: null,
            file_type: null,
            file_link: null,
        },
        type: Object,
    },
    attachmentLink: String,
    closable: {
        default: false,
        type: Boolean,
    },
});
</script>
