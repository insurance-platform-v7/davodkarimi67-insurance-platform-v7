
<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { quoteService } from "~/services/quote.service";
import { useToast } from "~/composables/useToast";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
const router = useRouter();
const toast = useToast();
const loading = ref(false);
const customerId = ref("");
const product = ref("");
const coverage = ref("");
const submit = async () => {
if (!customerId.value || !product.value || !coverage.value) return;
loading.value = true;
try {
const response = await quoteService.create({ customer_id: customerId.value, product: product.value, coverage: coverage.value });
toast.success("Quote created successfully");
const id = response.data?.id;
if (id) await router.push(`/quotes/${id}/premium`);
} finally {
loading.value = false;
}
};
</script>
<template>
<div>
<h1>Quote Wizard</h1>
<BaseSkeleton v-if="loading" />
<form v-else @submit.prevent="submit">
<label for="customer-id">Customer ID</label>
<input id="customer-id" v-model="customerId" required />
<label for="product">Product</label>
<input id="product" v-model="product" required />
<label for="coverage">Coverage</label>
<input id="coverage" v-model="coverage" required />
<button type="submit">Create Quote</button>
</form>
</div>
</template>
