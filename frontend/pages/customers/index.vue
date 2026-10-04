<script setup lang="ts">
import { usePermission } from "~/composables/usePermission";
import BaseSkeleton from "~/components/BaseSkeleton.vue";
import BaseEmptyState from "~/components/BaseEmptyState.vue";
import BaseTable from "~/components/BaseTable.vue";

const { can } = usePermission();
const loading = ref(false);
const customers = ref<Record<string, unknown>[]>([]);
const columns = ["name", "status"];
</script>
<template>
    <div>
        <h1>Customers</h1>
        <BaseSkeleton v-if="loading" />
        <BaseEmptyState v-else-if="customers.length === 0" message="No customers found" />
        <BaseTable v-else :rows="customers" :columns="columns" />
        <NuxtLink v-if="can('Customer.Create')" to="/customers/create">
            Create Customer
        </NuxtLink>
    </div>
</template>
