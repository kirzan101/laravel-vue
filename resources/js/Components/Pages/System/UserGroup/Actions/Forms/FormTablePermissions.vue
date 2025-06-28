<template>
    <c-container>
        <c-alert-error v-if="formErrors.permissions" :text="formErrors.permissions" />
        <v-row class="mt-1 mb-2 text-h6" justify="space-between">
            Permissions
        </v-row>
        <c-table density="compact">
            <thead>
                <tr>
                    <th class="text-left">Module</th>
                    <th class="text-left">All</th>
                    <th class="text-left">Read</th>
                    <th class="text-left">Create</th>
                    <th class="text-left">Update</th>
                    <th class="text-left">Delete</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="module in modules" :key="module">
                    <td>
                        <TextModuleName :name="module" />
                    </td>
                    <td>
                        <c-checkbox
                            color="primary"
                            :model-value="isChecked(module)"
                            @change="toggleAllPermissions(module)"
                        ></c-checkbox>
                    </td>
                    <td v-for="type in permissionTypes" :key="type">
                        <c-checkbox
                            color="primary"
                            v-model="selectedPermissions"
                            :disabled="!permissionItem(module, type)?.is_active"
                            :value="permissionItem(module, type)?.id"
                        ></c-checkbox>
                    </td>
                </tr>
            </tbody>
        </c-table>
    </c-container>
</template>

<script setup>
import { computed, ref, watch } from "vue";

import TextModuleName from "./Components/TextModuleName.vue";

const props = defineProps({
    userGroupPermissions: Array,
    permissions: Array,
    modules: Array,
    errors: Object,
    flash: Object,
    can: Array,
});

// set error start
const formErrors = ref({});
watch(
    () => props.errors,
    (newValue) => {
        formErrors.value = Object.assign({}, newValue);
    },
    { deep: true }
);
// set error end

// Array of permission types
const permissionTypes = ["view", "create", "update", "delete"];

// Ref to track selected permissions
const selectedPermissions = ref([]);

// get the disabled permission ids
const disabledPermissions = computed(() => {
    return props.permissions
        .filter((item) => item.is_active === false || item.is_active === 0)
        .map((item) => item.id);
});

watch(
    () => props.userGroupPermissions,
    (newUserGroupPermission) => {
        if (newUserGroupPermission && newUserGroupPermission.length > 0) {
            const activePermission = newUserGroupPermission
                .filter(
                    (permission) =>
                        permission.is_active === true ||
                        permission.is_active === 1
                )
                .map((item) => item.permission_id);

            selectedPermissions.value = [...activePermission];
        }
    },
    { immediate: true }
);

// Get the permission item for a specific module and type
const permissionItem = (module, type) => {
    return props.permissions.find(
        (item) => item.module === module && item.type === type
    );
};

// Check if all permissions are selected for a given module
const isChecked = (module) => {
    return permissionTypes.every((type) => {
        const permission = permissionItem(module, type);
        return permission && selectedPermissions.value.includes(permission.id);
    });
};

// Toggle selection of all permissions for a module
const toggleAllPermissions = (module) => {
    const allPermissions = permissionTypes
        .map((type) => permissionItem(module, type)?.id)
        .filter(Boolean);

    const allSelected = isChecked(module);

    if (allSelected) {
        // Deselect all if already selected
        selectedPermissions.value = selectedPermissions.value.filter(
            (id) => !allPermissions.includes(id)
        );
    } else {
        // Add permissions if not already selected
        selectedPermissions.value = [
            ...new Set([...selectedPermissions.value, ...allPermissions]),
        ];

        //  remove disabled permission id
        const filteredSelectedPermissions = selectedPermissions.value.filter(
            (item) => !disabledPermissions.value.includes(item)
        );

        selectedPermissions.value = [...filteredSelectedPermissions];
    }
};

// Watch for changes to update the select-all checkbox state
watch(selectedPermissions, () => {
    props.modules.forEach((module) => {
        isChecked(module);
    });
});

const emits = defineEmits(["selectedPermissions"]);
const emitPermissionsData = () => {
    emits("selectedPermissions", selectedPermissions.value);
};

defineExpose({
    emitPermissionsData,
});
</script>
