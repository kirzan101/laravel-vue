<template>
    <v-chip-group>
        <c-chip v-for="role in roleLists" :key="role.id">
            {{ role.name }}
        </c-chip>
    </v-chip-group>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    roles: Array,
    role_ids: Array,
});

const roleLists = computed(() => {
    return props.roles.filter((role) => props.role_ids.includes(role.id));
});
</script>
