<script setup lang="ts">
import BaseSkeleton from "~/components/BaseSkeleton.vue";
const loading = ref(false);
definePageMeta({ middleware: ["auth"] });
</script>
<template>
    <div>
        <h1>Dashboard</h1>
        <BaseSkeleton v-if="loading" />
        <div v-else>Dashboard content</div>
    </div>
</template>
