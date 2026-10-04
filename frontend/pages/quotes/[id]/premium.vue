<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { quoteService } from "~/services/quote.service";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
import BaseEmptyState from "~/components/BaseEmptyState.vue";

const route = useRoute();
const loading = ref(true);
const result = ref<unknown | null>(null);

onMounted(async () => {
  try {
    const response = await quoteService.get(String(route.params.id));
    result.value = response.data ?? null;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div>
    <h1>Premium Result</h1>
    <BaseSkeleton v-if="loading" />
    <BaseEmptyState v-else-if="!result" message="Premium result not found" />
    <pre v-else aria-label="Premium Result">{{ result }}</pre>
  </div>
</template>
