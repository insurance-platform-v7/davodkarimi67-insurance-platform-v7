
<script setup lang="ts">
import { ref, onMounted } from "vue";
import { policyService } from "~/services/policy.service";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
import BaseEmptyState from "~/components/BaseEmptyState.vue";
const loading = ref(true);
const policies = ref<Record<string, unknown>[]>([]);
onMounted(async () => {
try { const response = await policyService.list(); policies.value = response.data ?? []; } finally { loading.value = false; }
});
</script>
<template>
<div>
<h1>Policies</h1>
<NuxtLink to="/policies/issue">Issue Policy</NuxtLink>
<BaseSkeleton v-if="loading" />
<BaseEmptyState v-else-if="policies.length === 0" message="No policies found" />
<ul v-else>
<li v-for="(policy, index) in policies" :key="String(policy.id ?? index)">
<NuxtLink :to="`/policies/${policy.id}`">Policy {{ policy.id }}</NuxtLink>
</li>
</ul>
</div>
</template>
