<template>
    <v-container>
        <c-card>
            <table-user-group
                :errors="errors"
                :flash="flash"
                :can="can"
                ref="tableDataRef"
            />
        </c-card>
    </v-container>
</template>

<script setup>
import CCard from "@/Components/Customs/Cards/CCard.vue";
import TableUserGroup from "./Tables/TableUserGroup.vue";
import { ref } from "vue";

// Define props
const props = defineProps({
    errors: Object,
    flash: Object,
    can: Array,
});

const tableDataRef = ref(null);
const toggleLoadData = () => {
    if (tableDataRef.value) {
        tableDataRef.value.loadData();
    }
};
</script>
