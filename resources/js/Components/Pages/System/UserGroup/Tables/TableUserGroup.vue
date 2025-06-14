<template>
    <v-data-table-server
        :items="userGroups"
        :items-per-page="perPage"
        :headers="headers"
        :items-length="total"
        :loading="isLoading"
        :search="search"
        :items-per-page-options="itemsPerPageOption"
        :hover="true"
        item-value="id"
        @update:options="handleFilter"
    >
    </v-data-table-server>
</template>

<script setup>
import { ref } from "vue";
import axiosInstance from "@/Utilities/axios";

// Define props
const props = defineProps({
    errors: Object,
    flash: Object,
    can: Array,
});

// Define state variables
const userGroups = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const perPage = ref(10);
const sortBy = ref(null);
const sortDirection = ref(null);
const total = ref(0);
const isLoading = ref(true);

const search = ref(null);

const headers = ref([
    {
        title: "Name",
        align: "start",
        sortable: false,
        key: "name",
    },
    {
        title: "Code",
        align: "start",
        sortable: false,
        key: "code",
    },
    {
        title: "Description",
        align: "start",
        sortable: false,
        key: "description",
    },
]);

const itemsPerPageOption = [10, 25, 50, 100];

const loadData = async (setFilters = {}) => {
    try {
        const searchParams = new URLSearchParams();

        const filters = {
            current_page: currentPage.value ?? 1,
            per_page: perPage.value ?? 10,
            sort_by: sortBy.value ?? "id",
            sort_direction: sortDirection.value ?? "desc",
        };

        // Add default filters
        Object.entries(filters).forEach(([key, value]) => {
            searchParams.append(key, value ?? "");
        });

        // Add extra filters if passed
        if (
            setFilters &&
            typeof setFilters === "object" &&
            Object.keys(setFilters).length > 0
        ) {
            Object.entries(setFilters).forEach(([key, value]) => {
                searchParams.append(key, value ?? "");
            });
        }

        if (search.value && search.value.length > 3) {
            searchParams.append("search", search.value);
        }

        const response = await axiosInstance.get(
            "/user-groups?" + searchParams.toString()
        );

        const { data } = response.data; // Extract data directly from the response
        userGroups.value = data; // Set userGroups

        const {
            current_page,
            last_page,
            per_page,
            sort_by,
            sort_direction,
            total: totalCount,
        } = response.data;

        // Set pagination and sorting data
        currentPage.value = current_page;
        lastPage.value = last_page;
        perPage.value = per_page;
        sortBy.value = sort_by;
        sortDirection.value = sort_direction;
        total.value = totalCount;
    } catch (error) {
        console.error("Error fetching user groups:", error);
        // Optionally, you could set an error state here
    } finally {
        // Always set loading to false, regardless of success or failure
        isLoading.value = false;
    }
};

const handleFilter = async ({ page, itemsPerPage, sortBy: sortParams }) => {
    currentPage.value = page;
    perPage.value = itemsPerPage;

    // get the first value
    if (sortParams[0]) {
        sortBy.value = sortParams[0].key;
        sortDirection.value = sortParams[0].order;
    } else {
        // return to default
        sortBy.value = null;
        sortDirection.value = null;
    }

    isLoading.value = true;

    await loadData();
};

defineExpose({
    loadData,
});
</script>
