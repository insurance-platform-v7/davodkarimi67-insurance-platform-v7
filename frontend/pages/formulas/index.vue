<script setup lang="ts">
import { ref, onMounted } from "vue";
import { formulaService } from "~/services/formula.service";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
import BaseEmptyState from "~/components/BaseEmptyState.vue";

const loading = ref(false);
const formulas = ref<unknown[]>([]);

const loadFormulas = async () => {
  loading.value = true;
  try {
    const response = await formulaService.list();
    formulas.value = Array.isArray(response.data) ? response.data : [];
  } finally {
    loading.value = false;
  }
};

onMounted(loadFormulas);
</script>

<template>
  <div>
    <h1>Formulas</h1>
    <BaseSkeleton v-if="loading" />
    <BaseEmptyState v-else-if="formulas.length === 0" message="No formulas found" />
    <ul v-else>
      <li v-for="(formula, index) in formulas" :key="index">{{ formula }}</li>
    </ul>
  </div>
</template>
