<script setup lang="ts">
import { ref } from "vue";
const loading = ref(false);
const revenue = ref(0);
const policies = ref(0);
const quotes = ref(0);
definePageMeta({ middleware: ["auth"] });
</script>
<template>
<div>
<h1>Dashboard</h1>
<BaseSkeleton v-if="loading" />
<div v-else>
<RevenueWidget :value="revenue" />
<PolicyWidget :value="policies" />
<QuoteWidget :value="quotes" />
</div>
</div>
</template>
