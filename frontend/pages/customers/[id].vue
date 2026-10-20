
<script setup lang="ts">
import { ref } from "vue";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
import BaseEmptyState from "~/components/BaseEmptyState.vue";
const loading = ref(false);
const customer = ref<unknown | null>(null);
</script>
<template>
<div>
<h1>Customer</h1>
<BaseSkeleton v-if="loading" />
<BaseEmptyState v-else-if="!customer" message="Customer not found" />
<div v-else>Customer details</div>
</div>
</template>
