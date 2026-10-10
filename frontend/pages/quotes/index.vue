<script setup lang="ts">
import { ref, onMounted } from "vue";
import { quoteService } from "~/services/quote.service";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
import BaseEmptyState from "~/components/BaseEmptyState.vue";

const loading = ref(true);
const quotes = ref<Record<string, unknown>[]>([]);

onMounted(async () => {
  try {
    const response = await quoteService.list();
    quotes.value = response.data ?? [];
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div>
    <h1>Quotes</h1>
    <BaseSkeleton v-if="loading" />
    <BaseEmptyState v-else-if="quotes.length === 0" message="No quotes found" />
    <ul v-else>
      <li v-for="(quote, index) in quotes" :key="String(quote.id ?? index)">
        <NuxtLink :to="`/quotes/${quote.id}`">Quote {{ quote.id }}</NuxtLink>
      </li>
    </ul>
  </div>
</template>
