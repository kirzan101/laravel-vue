<template>
    <v-dialog v-model="dialog" max-width="320" persistent>
        <v-list class="py-2" color="primary" elevation="12" rounded="lg">
            <v-list-item prepend-icon="mdi-information" title="Loading...">
                <template v-slot:prepend>
                    <div class="pe-4">
                        <v-icon> mdi-run-fast </v-icon>
                    </div>
                </template>

                <template v-slot:append>
                    <v-progress-circular
                        color="primary"
                        indeterminate="disable-shrink"
                        size="20"
                        width="3"
                    ></v-progress-circular>
                </template>
            </v-list-item>
        </v-list>
    </v-dialog>
</template>

<script setup>
import { ref } from "vue";

const dialog = ref(false);

const setVisible = (status = false) => {
    dialog.value = !!status;
};

defineExpose({ setVisible });
</script>
