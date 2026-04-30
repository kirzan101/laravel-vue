<template>
    <c-combobox
        label="User Group"
        :items="userGroupItems"
        item-value="value"
        item-title="title"
        :error-messages="errors.user_group_id"
        :return-object="false"
        v-bind="$attrs"
    ></c-combobox>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    user_groups: Array,
    errors: Object,
});

const userGroupItems = computed(() => {
    return props.user_groups.map((group) => ({
        value: group.id,
        title: group.name,
    }));
});
</script>
