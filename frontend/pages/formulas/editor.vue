<script setup lang="ts">
import { ref, computed } from "vue";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
import { formulaService } from "~/services/formula.service";

const loading = ref(false);
const formula = ref("price * rate");
const validationError = ref("");
const previewResult = ref<unknown | null>(null);

const highlightedTokens = computed(() => formula.value.split(/(price|rate|premium)/g));

const validateFormula = () => {
  validationError.value = formula.value.trim() ? "" : "Formula is required";
  return !validationError.value;
};

const preview = async () => {
  if (!validateFormula()) return;
  loading.value = true;
  try {
    const response = await formulaService.execute({ formula: formula.value, preview: true });
    previewResult.value = response.data;
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div>
    <h1>Formula Editor</h1>
    <label for="formula">Formula</label>
    <textarea id="formula" v-model="formula" @input="validateFormula" />
    <pre aria-label="Syntax Highlight"><template v-for="(token, index) in highlightedTokens" :key="index"><mark v-if="/^(price|rate|premium)$/.test(token)">{{ token }}</mark><span v-else>{{ token }}</span></template></pre>
    <p v-if="validationError" role="alert">{{ validationError }}</p>
    <button type="button" @click="validateFormula">Validate</button>
    <button type="button" @click="preview" :disabled="loading">Preview</button>
    <BaseSkeleton v-if="loading" />
    <pre v-else-if="previewResult !== null" aria-label="Formula Preview">{{ previewResult }}</pre>
  </div>
</template>
