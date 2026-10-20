
<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { policyService } from "~/services/policy.service";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
import BaseEmptyState from "~/components/BaseEmptyState.vue";
const route = useRoute();
const loading = ref(true);
const policy = ref<Record<string, unknown> | null>(null);
onMounted(async () => {
try { const response = await policyService.get(String(route.params.id)); policy.value = response.data ?? null; } finally { loading.value = false; }
});
</script>
<template>
<div>
<h1>Policy Details</h1>
<BaseSkeleton v-if="loading" />
<BaseEmptyState v-else-if="!policy" message="Policy not found" />
<pre v-else>{{ policy }}</pre>
</div>
</template>
