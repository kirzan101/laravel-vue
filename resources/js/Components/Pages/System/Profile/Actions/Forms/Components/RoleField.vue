<template>
    <c-combobox
        item-title="name"
        item-value="id"
        multiple
        label="Roles"
        v-bind="$attrs"
        chips
        clearable
        :items="roleItems"
        :error-messages="errors.role_ids"
        :return-object="false"
    />
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    roles: Array,
    errors: Object,
});

const roleItems = computed(() => {
    return props.roles.map((role) => ({
        id: role.id,
        name: role.name,
    }));
});
</script>
