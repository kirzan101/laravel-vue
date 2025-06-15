<template>
    <c-fab
        v-bind="$attrs"
        icon="mdi-plus"
        location="center end"
        absolute
        size="x-large"
        style="position: fixed; top: 350px; right: 25px; z-index: 1000"
    >
        <template
            v-for="(_, name) in $slots"
            :key="name"
            v-slot:[name]="slotProps"
        >
            <slot :name="name" v-bind="slotProps" />
        </template>
    </c-fab>
</template>

<script setup>
import CFab from "./CFab.vue";
</script>
