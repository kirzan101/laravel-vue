<template>
    <c-combobox
        v-model="innerValue"
        item-value="id"
        item-title="name"
        label="User Group"
        :items="userGroupItems"
        v-bind="$attrs"
    />
</template>

<script setup>
import { onMounted, ref, watch, computed } from "vue";
import axiosInstance from "@/Utilities/axios";

// Expose v-model
const props = defineProps({
    modelValue: [Number, String, null],
});

const emit = defineEmits(["update:modelValue"]);

const innerValue = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const userGroupItems = ref([]);
const isLoading = ref(false);

const loadUserGroups = async () => {
    if (isLoading.value) return;
    isLoading.value = true;

    try {
        const response = await axiosInstance.get("/user-groups/search");
        const responseData = response.data;
        const data = responseData.data ?? responseData;

        if (!Array.isArray(data)) {
            console.error(
                "API response for user groups is not an array.",
                response.data
            );
            return;
        }

        userGroupItems.value = data.map((group) => ({
            id: group.id,
            name: group.name,
        }));

        // ✅ ensure selected item is in list
        if (
            props.modelValue &&
            !userGroupItems.value.find((i) => i.id == props.modelValue)
        ) {
            const selectedResponse = await axiosInstance.get(
                `/user-groups/search?id=${props.modelValue}`
            );
            const selected =
                selectedResponse.data.data ?? selectedResponse.data;
            if (selected) {
                userGroupItems.value.push({
                    id: selected.id,
                    name: selected.name,
                });
            }
        }
    } catch (error) {
        console.error("Failed to load user groups:", error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    loadUserGroups();
});
</script>
