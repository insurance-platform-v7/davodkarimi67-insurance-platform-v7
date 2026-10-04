<script setup lang="ts">
import { ref } from "vue";
import { policyService } from "~/services/policy.service";
import { useToast } from "~/composables/useToast";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
const toast = useToast();
const loading = ref(false);
const customerId = ref("");
const product = ref("");
const issue = async () => {
if (!customerId.value || !product.value) return;
loading.value = true;
try { await policyService.issue({ customer_id: customerId.value, product: product.value }); toast.success("Policy issued successfully"); } finally { loading.value = false; }
};
</script>
<template>
<div>
<h1>Issue Policy</h1>
<BaseSkeleton v-if="loading" />
<form v-else @submit.prevent="issue">
<label for="customer-id">Customer ID</label>
<input id="customer-id" v-model="customerId" required />
<label for="product">Product</label>
<input id="product" v-model="product" required />
<button type="submit">Issue Policy</button>
</form>
</div>
</template>
